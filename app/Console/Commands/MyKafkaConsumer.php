<?php

namespace App\Console\Commands;

use App\Factories\HandlerFactory;
use App\Models\UserStatistic;
use Illuminate\Console\Command;
use InvalidArgumentException;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;

class MyKafkaConsumer extends Command
{
    protected $signature = 'kafka:consume';

    protected $description = 'Consume messages from Kafka';

    public function handle()
    {
        $conf = new Conf;
        $conf->set('group.id', 'task_service_group');
        $conf->set('metadata.broker.list', env('KAFKA_BROKER'));
        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new KafkaConsumer($conf);
        $consumer->subscribe(['stat']); // Название топика

        echo "Waiting for messages...\n";

        while (true) {
            $message = $consumer->consume(120 * 1000); // Таймаут в миллисекундах

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $payload = json_decode($message->payload, true);

                    \Log::info(json_encode($payload['message'], JSON_PRETTY_PRINT));

                    $this->processMessage($payload['message']);
                    break;

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
            }
        }
    }

    private function processMessage($payload)
    {
        $this->info('Received smth');
        \Log::info(json_encode($payload, JSON_PRETTY_PRINT));

        $updateType = $payload['update_type'] ?? null;

        if ($updateType == 'status') {
            \Log::warning('in status change event');
            $this->processTaskStatus($payload);
        } else {
            \Log::warning('in stop/start change event');
            $this->processTimeInterval($payload, $updateType);
        }

    }

    private function processTimeInterval($payload, $updateType)
    {
        // Список обработчиков
        $handlers = ['UserStatistic', 'DailyStatistic'];

        foreach ($handlers as $handlerName) {
            $handler = HandlerFactory::getHandler($updateType, $handlerName);
            if ($handler) {
                $handler::handle($payload);
            } else {
                \Log::warning("Handler not found for type: {$updateType}, handler: {$handlerName}");
            }
        }
    }

    private function processTaskStatus($payload)
    {
        if (! isset($payload['user_id'])) {
            throw new InvalidArgumentException('Missing required fields in payload for status change event');
        }

        $statistic = UserStatistic::firstOrNew([
            'user_id' => $payload['user_id'],
        ]);

        $statistic->total_tasks_completed += match ($payload['new_status']) {
            'Completed' => 1,
            'In Progress' => -1,
            default => 0,
        };

        $statistic->save();
    }
}
