<?php
namespace App\Models;

use App\Enums\ComplaintStatus;
use App\Enums\ComplaintUrgency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'category_id',
        'reporter_name',
        'reporter_contact',
        'is_anonymous',
        'victim_relation',
        'chronology',
        'general_location',
        'urgency',
        'consent_at',
        'status',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'consent_at' => 'datetime',
            'status' => ComplaintStatus::class,
            'urgency' => ComplaintUrgency::class,
        ];
    }

    protected static function booted()
    {
        static::creating(function ($complaint) {
            if (empty($complaint->ticket_number)) {
                $complaint->ticket_number = 'KLA-' . date('Ym') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplaintAttachment::class, 'complaint_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(ComplaintFollowUp::class, 'complaint_id');
    }
}
