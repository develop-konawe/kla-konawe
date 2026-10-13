<?php
namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'category',
        'address',
        'coordinates',
        'status',
        'is_child_friendly',
        'verified_at',
        'verified_by',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'is_child_friendly' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
