<?php
namespace App\Models;

use App\Enums\AspirationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aspiration extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author_name',
        'author_age',
        'author_region',
        'status',
        'category',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AspirationStatus::class,
            'reviewed_at' => 'datetime',
            'author_age' => 'integer',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
