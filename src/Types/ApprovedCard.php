<?php

namespace Dinhdjj\Thesieure\Types;

class ApprovedCard
{
    public function __construct(
        public string $request_id,
        public string $telco,
        public int $declared_value,
        public string $serial,
        public string $code,
        public int $status,
        public ?int $value,
        public ?int $amount,
        public ?string $message,
    ) {
    }

    public function isApproving(): bool
    {
        return 99 === $this->status;
    }

    public function isSuccess(): bool
    {
        return \in_array($this->status, [1, 2], true);
    }

    public function isError(): bool
    {
        return \in_array($this->status, [3, 4, 100], true);
    }

    public function getReceivedValue(): int
    {
        if (!$this->isSuccess()) {
            return 0;
        }

        return $this->amount;
    }

    public function getRealFaceValue(): int
    {
        if (!$this->isSuccess()) {
            return 0;
        }

        return $this->value;
    }
}
