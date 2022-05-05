<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Button extends Model implements HasMedia
{
    use NodeTrait, InteractsWithMedia;

    const BACK = 'Назад';

    protected $table = 'buttons';

    protected $guarded = [];
}