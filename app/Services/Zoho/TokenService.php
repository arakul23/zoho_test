<?php

declare(strict_types=1);

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use RuntimeException;

class TokenService
{
    private const string MODULE_NAME = 'token';

    protected string $cacheKey = 'zoho:access_token';

    protected string $refreshTokenKey = 'zoho:refresh_token';

    public function getAccessToken(): string
    {
        if ($token = Redis::connection('zoho')->get($this->cacheKey)) {
            return $token;
        }

        return $this->refreshAccessToken();
    }

    public function refreshAccessToken(): string
    {
        $refreshToken = Redis::connection('zoho')->get($this->refreshTokenKey)
            ?? config('services.zoho.refresh_token');

        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => $refreshToken,
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'grant_type' => 'refresh_token',
        ]);

        $data = $response->json();
dd($data);
        if (! $response->ok() || empty($data['access_token'])) {
            throw new RuntimeException('Failed to refresh Zoho access token: '.($data['error'] ?? 'unknown error'));
        }

        $ttl = ($data['expires_in'] ?? 3600) - 60;

        Redis::connection('zoho')->setex($this->cacheKey, $ttl, $data['access_token']);

        return $data['access_token'];
    }
}
