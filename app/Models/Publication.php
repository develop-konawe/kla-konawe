<?php
namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\PublicationClassification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'year',
        'file_path',
        'classification',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'classification' => PublicationClassification::class,
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
