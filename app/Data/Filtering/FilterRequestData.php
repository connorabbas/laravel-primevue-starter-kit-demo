<?php

namespace App\Data\Filtering;

use Spatie\LaravelData\Data;

class FilterRequestData extends Data
{
    public function __construct(
        public ?string $matchMode,
        public mixed $value,
    ) {
    }
}
