<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use App\Handlers\UserStatisticHandler;

class MyKafkaConsumer extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Consume messages from Kafka';

    public function handle()
    {
        $conf = new Conf();
        $conf->set('group.id', 'task_service_group');
        $conf->set('metadata.broker.list', 'localhost:9092');
        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new KafkaConsumer($conf);
        $consumer->subscribe(['stat']); // Название топика

        echo "Waiting for messages...\n";

        while (true) {
            $message = $consumer->consume(10 * 1000); // Таймаут в миллисекундах

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $payload = json_decode($message->payload, true);

                    \Log::info("data: {$message->payload}");
                    $this->processMessage($payload);
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
        $this->info("Received smth");
        $this->info("type: {$payload['type']}");

        UserStatisticHandler::handle($payload);
    }
}