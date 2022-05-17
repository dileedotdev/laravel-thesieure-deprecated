<?php

namespace Dinhdjj\Thesieure\Types;

class FetchedCardType
{
    public function __construct(
        public string $telco,
        public int $value,
        public int $fees,
        public int $penalty,
    ) {
    }
}
