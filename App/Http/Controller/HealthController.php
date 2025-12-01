<?php

namespace App\Http\Controller;

use App\Application\Health\CheckHealthService;
use App\Http\Response\JsonResponder;

class HealthController
{
    public function __construct(
        private CheckHealthService $checkHealthService,
        private JsonResponder $responder
    )
    {
    }

    public function check(): void
    {
        $result = $this->checkHealthService->execute();

        $this->responder->respond([
            'status' => $result->getStatus(),
            'timestamp' => $result->getCheckedAt()->format(DATE_ATOM),
        ]);
    }
}
