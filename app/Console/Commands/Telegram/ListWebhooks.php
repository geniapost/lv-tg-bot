<?php

namespace App\Console\Commands\Telegram;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class ListWebhooks extends Command
{
    protected $signature = 'telegram:list_webhooks';

    protected $description = 'List webhooks for telegram bot';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try{
            $telegram = app()->make(\Telegram\Bot\Api::class);
            $token = $telegram->getAccessToken();
            $client = new Client(
                ['base_uri' => "https://api.telegram.org/bot$token/"]
            );

            $result = $client->request('POST', 'getWebhookInfo');
            $response = json_decode($result->getBody()->getContents());
            dd($response);
        }catch (\Exception $exception)
        {
            dd($exception->getMessage());
        }
    }
}