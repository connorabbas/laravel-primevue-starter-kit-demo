<?php

namespace App\Data;

use App\Models\Contact;
use App\Models\Organization;
use App\Models\Tag;
use Carbon\CarbonImmutable;
use LogicException;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ContactData extends Data
{
    /**
     * @param array<int, TagData> $tags
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public ?int $organizationId,
        public CarbonImmutable|string $createdAt,
        public CarbonImmutable|string $updatedAt,
        public ?OrganizationData $organization,
        #[DataCollectionOf(TagData::class)]
        public array $tags,
    ) {
    }

    public static function fromModel(Contact $contact): self
    {
        $createdAt = $contact->created_at;
        $updatedAt = $contact->updated_at;

        if ($createdAt === null || $updatedAt === null) {
            throw new LogicException('Contact timestamps must be present before transforming to ContactData.');
        }

        $organization = null;

        if ($contact->relationLoaded('organization')) {
            $relatedOrganization = $contact->getRelation('organization');

            if ($relatedOrganization instanceof Organization) {
                $organization = OrganizationData::fromModel($relatedOrganization);
            }
        }

        $tags = [];

        if ($contact->relationLoaded('tags')) {
            $tags = $contact->getRelation('tags')
                ->filter(fn (mixed $tag): bool => $tag instanceof Tag)
                ->map(fn (Tag $tag): TagData => TagData::fromModel($tag))
                ->values()
                ->all();
        }

        return new self(
            id: $contact->id,
            name: $contact->name,
            email: $contact->email,
            phone: $contact->phone,
            organizationId: $contact->organization_id,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            organization: $organization,
            tags: $tags,
        );
    }
}
