<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Button extends Model
{
    use NodeTrait;

    const BACK = 'Назад';

    protected $table = 'buttons';

    protected $guarded = [];
}