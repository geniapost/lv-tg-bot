<?php

namespace App\Listeners\Observers\Telegram;

use App\Models\Telegram\Button;
use Illuminate\Http\Request;

class ButtonObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function creating(Button $button)
    {
        //
    }

    public function updated(Button $button)
    {

    }
}