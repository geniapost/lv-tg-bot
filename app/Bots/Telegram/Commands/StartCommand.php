<?php

namespace App\Bots\Telegram\Commands;

use App\Models\Telegram\Button;
use App\Models\Telegram\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle($arguments = null)
    {
        $reply = app()->make(Api::class)->getWebhookUpdates()['message']['from'];
        $user = User::create($reply);

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $keyboard = [];
        $request_body = [
            'chat_id' => $user->chat_id,
        ];

        $root_buttons = Button::whereNull('parent_id')->get();

        if($root_buttons->count())
        {
            foreach ($root_buttons as $root_button)
            {
                array_unshift($keyboard, [$root_button->title]);
            }

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $request_body = $request_body + [
                    'text' => 'Вітаємо',
                    'reply_markup' => $reply_markup
                ];
        }else{
            $request_body = $request_body + [
                    'text' => 'Наразі немає опцій'
                ];
        }

        $response = app()->make(Api::class)->sendMessage($request_body);
    }

    public function defaultHandle()
    {
        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
        $this->replyWithMessage(['text' => 'Вітаємо']);

        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // This will prepare a list of available commands and send the user.
        // First, Get an array of all registered commands
        // They'll be in 'command-name' => 'Command Handler Class' format.
        $commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }
//
//        // Reply with the commands list
        $this->replyWithMessage(['text' => $response]);

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        $this->triggerCommand('subscribe');
    }
}