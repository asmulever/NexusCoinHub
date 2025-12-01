<?php

namespace App\Infrastructure\Logging;

use App\Shared\Contracts\LoggerInterface;

class ErrorLogLogger implements LoggerInterface
{
    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $payload = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];

        error_log(json_encode($payload));
    }
}
