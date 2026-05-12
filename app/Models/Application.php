<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'scheme_id',
        'conversation_id',
        'status',
        'interview_data',
        'notes',
        'remarks',
        'submitted_at',
        'sla_deadline',
    ];

    protected function casts(): array
    {
        return [
            'interview_data' => 'array',
            'submitted_at'   => 'datetime',
            'sla_deadline'   => 'datetime',
        ];
    }

    const STATUS_DRAFT        = 'draft';
    const STATUS_SUBMITTED    = 'submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED     = 'approved';
    const STATUS_REJECTED     = 'rejected';

    public function scopeOverdueSla($query)
    {
        return $query
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', now())
            ->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW]);
    }

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
