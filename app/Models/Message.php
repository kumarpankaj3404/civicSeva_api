<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'context_metadata',
    ];

    protected function casts(): array
    {
        return [
            'context_metadata' => 'array',
        ];
    }

    // Roles
    const ROLE_USER      = 'user';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_SYSTEM    = 'system';

    // ─── Relationships ────────────────────────────────────────────────────────────

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}
