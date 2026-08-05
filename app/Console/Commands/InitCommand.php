<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Command\Command as CommandAlias;

class InitCommand extends Command
{
    protected $signature = 'app:init-command {code : Grant code from Zoho Self Client}';

    protected $description = 'Exchanges grant code for refresh token and stores it in Redis';

    public function handle(): int
    {
        $response = Http::asForm()->post(config('services.zoho.refresh_token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'code' => $this->argument('code'),
        ]);

        $data = $response->json();

        if (empty($data['refresh_token'])) {
            $this->error('Failed: '.json_encode($data));

            return Command::FAILURE;
        }

        Redis::connection('zoho')->set('zoho:refresh_token', $data['refresh_token']);

        $ttl = max(($data['expires_in'] ?? 3600) - 300, 60);
        Redis::connection('zoho')->setex('zoho:access_token', $ttl, $data['access_token']);

        $this->info('Zoho refresh token successfully saved to Redis!');

        return CommandAlias::SUCCESS;
    }
}
