<?php

namespace App\Models;

use App\Models\Telegram\Button;
use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    const PROVIDER_TELEGRAM = 'telegram';

    protected $table = 'user_history';
    protected $guarded = [];

    public function button()
    {
        return $this->belongsTo(Button::class)->first();
    }
}