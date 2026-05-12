<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'title',
        'status',
        'interview_progress',
    ];

    protected function casts(): array
    {
        return [
            'interview_progress' => 'array',
        ];
    }

    const STATUS_ACTIVE   = 'active';
    const STATUS_CLOSED   = 'closed';
    const STATUS_ARCHIVED = 'archived';

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
