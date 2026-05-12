<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
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

    const ROLE_USER      = 'user';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_SYSTEM    = 'system';

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}
