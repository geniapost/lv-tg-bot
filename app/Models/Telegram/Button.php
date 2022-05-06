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

    public function getNeighbors()
    {
        if(is_null($this->parent_id))
        {
            $result = Button::where('id', '!=', $this->id)->whereNull('parent_id');
        }else{
            $result = Button::where('id', '!=', $this->id)->where('parent_id', $this->parent_id);
        }

        return $result->pluck('title', 'id')->toArray();
    }
}