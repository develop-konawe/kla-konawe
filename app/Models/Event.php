<?php
namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_at',
        'end_at',
        'location',
        'organizer',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }
}
