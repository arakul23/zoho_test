<?php

declare(strict_types=1);

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use RuntimeException;

class TokenService
{
    protected string $accessTokenKey = 'zoho:access_token';

    protected string $refreshTokenKey = 'zoho:refresh_token';

    public function getAccessToken(): string
    {
        if ($token = Redis::connection('zoho')->get($this->accessTokenKey)) {
            return $token;
        }

        return $this->refreshAccessToken();
    }

    public function refreshAccessToken(): string
    {
        $refreshToken = Redis::connection('zoho')->get($this->refreshTokenKey);

        $response = Http::asForm()->post(config('services.zoho.refresh_token_url'), [
            'refresh_token' => $refreshToken,
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'grant_type' => 'refresh_token',
        ]);

        $data = $response->json();

        if (! $response->ok() || empty($data['access_token'])) {
            throw new RuntimeException('Failed to refresh Zoho access token: '.($data['error'] ?? 'unknown error'));
        }

        $ttl = ($data['expires_in'] ?? 3600) - 60;

        Redis::connection('zoho')->setex($this->accessTokenKey, $ttl, $data['access_token']);

        return $data['access_token'];
    }
}
