<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'telegram_users';
    protected $guarded = [];

    public static function create(array $user) : User
    {
        return User::firstOrCreate(
            ['chat_id' => $user['id']],
            [
                'is_bot' => $user['is_bot'],
                'first_name' => $user['first_name'] ?? null,
                'username' => $user['username'] ?? null,
            ]
        );
    }
}