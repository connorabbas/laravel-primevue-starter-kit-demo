<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * @return MorphToMany<Organization, $this>
     */
    public function organizations(): MorphToMany
    {
        return $this->morphedByMany(Organization::class, 'taggable');
    }

    /**
     * @return MorphToMany<Contact, $this>
     */
    public function contacts(): MorphToMany
    {
        return $this->morphedByMany(Contact::class, 'taggable');
    }
}
