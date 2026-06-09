<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'gst_number',
        'plan',
        'status',
        'settings',
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function jobCategories(): HasMany
    {
        return $this->hasMany(JobCategory::class);
    }

    protected $casts = [
        'settings' => 'array',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }
}
