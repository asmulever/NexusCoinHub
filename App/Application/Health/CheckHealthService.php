<?php

namespace App\Application\Health;

use App\Domain\Health\HealthStatus;
use DateTimeImmutable;

class CheckHealthService
{
    public function execute(): HealthStatus
    {
        return new HealthStatus('ok', new DateTimeImmutable());
    }
}
