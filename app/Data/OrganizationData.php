<?php

namespace App\Data;

use App\Models\Organization;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class OrganizationData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $industry,
        public ?string $website,
    ) {
    }

    public static function fromModel(Organization $organization): self
    {
        return new self(
            id: $organization->id,
            name: $organization->name,
            industry: $organization->industry,
            website: $organization->website,
        );
    }
}
