<?php

namespace App\Http\Handler\Telegram;

use App\Models\Telegram\Button;
use App\Models\Telegram\User;
use App\Models\UserHistory;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class ButtonHandler
{
    protected $user;
    protected $keyboard;
    protected $reply_markup;
    protected $message;

    function __construct()
    {
        $this->keyboard = [[Button::BACK]];
        $this->message = '';
    }

    public function handle(Update $response)
    {
        $message = $response->getMessage();
        $from = $message->get('from');
        $this->user = User::create($from->toArray());

        $text = $message->get('text');
        $clicked_button = Button::where('title', $text)->first();

        if(mb_substr($text, 0, 1, 'utf-8') != '/')
        {
            if($text == Button::BACK)
            {
                $this->backButtonHandle();
            }else{
                if(is_null($clicked_button))
                {
                    $this->message = 'Даної опції не існує';
                    $this->send();
                }else
                {
                    $this->handleClick($clicked_button);
                }
            }
        }
    }

    public function backButtonHandle()
    {
        $last_click = UserHistory::where('user_id', $this->user->id)
            ->where('provider', UserHistory::PROVIDER_TELEGRAM)
            ->latest()
            ->first();

        if(!is_null($last_click))
        {
            $last_button = $last_click->button();

            if(!is_null($last_button->parent_id))
            {
                $button = Button::find($last_button->parent_id);
                $this->handleClick($button);
            }else{
                $this->handleRootButtons();
            }
        }else{
            $this->message = 'Даної опції не існує';
            $this->send();
        }
    }

    public function handleClick(Button $clicked_button)
    {
        $this->message = $clicked_button->text;

        if(count($clicked_button->children()->pluck('id')))
        {
            $children = Button::defaultOrder()->find($clicked_button->children()->pluck('id'));
            foreach ($children as $child)
            {
                array_unshift($this->keyboard, [$child->title]);
            }
        }

        $this->send();

        if(!is_null($clicked_button->image))
        {
            $this->sendPhoto($clicked_button->image);
        }

        if(!is_null($clicked_button->file))
        {
            $this->sendFile($clicked_button->file);
        }

        UserHistory::create([
            'user_id' => $this->user->id,
            'button_id' => $clicked_button->id,
        ]);
    }

    public function handleRootButtons()
    {
        $root_buttons = Button::whereNull('parent_id')->defaultOrder()->get();

        if($root_buttons->count())
        {
            $this->keyboard = [];
            foreach ($root_buttons as $root_button)
            {
                array_unshift($this->keyboard, [$root_button->title]);
                $this->message = 'Вітаємо';
            }
        }else{
            $this->message = 'Наразі немає опцій';
        }

        $this->send();
    }

    public function sendPhoto($path)
    {
        app()->make(Api::class)->sendPhoto([
            'chat_id' => $this->user->chat_id,
            'photo' => new InputFile(storage_path().'/app/public/'.$path),
        ]);
    }

    public function sendFile($path)
    {
        app()->make(Api::class)->sendDocument([
            'chat_id' => $this->user->chat_id,
            'document' => new InputFile(storage_path().'/app/public/'.$path),
        ]);
    }

    public function send()
    {
        $this->reply_markup = Keyboard::make([
            'keyboard' => $this->keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $response = app()->make(Api::class)->sendMessage([
            'text' => $this->message,
            'chat_id' => $this->user->chat_id,
            'reply_markup' => $this->reply_markup,
            'parse_mode' => 'Markdown',
        ]);
    }
}