<?php
namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statistic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'year',
        'region_id',
        'value',
        'unit',
        'source',
        'status',
        'metadata_json',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'metadata_json' => 'array',
            'value' => 'decimal:2',
            'year' => 'integer',
        ];
    }
}
