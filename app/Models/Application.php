<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Application extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'applications';

    protected $fillable = [
        'user_id',
        'scheme_id',
        'conversation_id',
        'status',
        'interview_data',
        'blockchain_hash',
        'blockchain_logged_at',
        'sla_deadline',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'interview_data'       => 'array',
            'blockchain_logged_at' => 'datetime',
            'sla_deadline'         => 'datetime',
        ];
    }

    // Statuses
    const STATUS_DRAFT         = 'draft';
    const STATUS_SUBMITTED     = 'submitted';
    const STATUS_UNDER_REVIEW  = 'under_review';
    const STATUS_APPROVED      = 'approved';
    const STATUS_REJECTED      = 'rejected';

    // ─── Scopes ───────────────────────────────────────────────────────────────────

    public function scopeOverdueSla($query)
    {
        return $query
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', now())
            ->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW]);
    }

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}
