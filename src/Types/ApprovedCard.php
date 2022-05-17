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
}
