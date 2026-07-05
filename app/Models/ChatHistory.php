<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $table = 'chat_history';

    protected $fillable = ['user_id', 'user_message', 'bot_response', 'conversation_context'];

    protected $casts = [
        'conversation_context' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
