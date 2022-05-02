<?php

namespace App\Console\Commands\Telegram;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class SetWebhook extends Command
{
    protected $signature = 'telegram:set_webhook {url?}';

    protected $description = 'Set webhook for telegram bot';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try{
            $url = $this->argument('url') ?? env('APP_URL');

            $telegram = app()->make(\Telegram\Bot\Api::class);
            $token = $telegram->getAccessToken();
            $client = new Client(
                ['base_uri' => "https://api.telegram.org/bot$token/"]
            );

            $result = $client->request('POST', 'setWebhook', ['query' => ['url' => rtrim($url, '/')]]);
            $response = json_decode($result->getBody()->getContents());

            if($response->ok && $response->result)
            {
                $this->info($response->description);
            }else{
                $this->info('webhook not set');
            }
        }catch (\Exception $exception)
        {
            $this->info('webhook not set');
            dd($exception->getMessage());
        }
    }
}