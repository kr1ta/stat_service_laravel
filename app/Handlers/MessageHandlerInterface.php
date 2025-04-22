<?php

namespace App\Handlers;

interface MessageHandlerInterface
{
    public static function handle(array $payload): void;
}
