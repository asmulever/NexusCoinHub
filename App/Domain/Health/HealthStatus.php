<?php

namespace App\Domain\Health;

use DateTimeImmutable;

class HealthStatus
{
    public function __construct(
        private string $status,
        private DateTimeImmutable $checkedAt
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCheckedAt(): DateTimeImmutable
    {
        return $this->checkedAt;
    }
}
