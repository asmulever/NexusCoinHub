<?php

namespace App\Http\Response;

class JsonResponder
{
    /**
     * Emit a JSON response with proper headers.
     *
     * @param array<string, mixed> $payload
     */
    public function respond(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }
}

