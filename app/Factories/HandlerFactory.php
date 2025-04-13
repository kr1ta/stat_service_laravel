<?php

namespace App\Factories;

use App\Handlers\MessageHandlerInterface;

class HandlerFactory
{
    public static function getHandler(string $type, string $handlerName): ?MessageHandlerInterface
    {
        $className = "\\App\\Handlers\\" . ucfirst($handlerName) . ucfirst($type) . "Handler";
        \Log::info("Looking for handler class: {$className}");

        if (class_exists($className)) {
            \Log::info("Handler class found: {$className}");
            return new $className();
        }

        \Log::warning("Handler class not found: {$className}");
        return null;
    }
}