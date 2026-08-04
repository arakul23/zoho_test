<?php

declare(strict_types=1);

namespace App\Services\Zoho;

use GuzzleHttp\Client;

readonly class DealService
{
    private const string MODULE_NAME = 'Deals';

    private string $apiUrl;

    private string $token;

    public function __construct(private Client $client, private TokenService $tokenService) {
        $this->apiUrl = config('services.zoho.url') . '/' . self::MODULE_NAME;
        $this->token = $this->tokenService->getAccessToken();
    }

    public function create(array $data, int $accountId) {
        $this->client->post($this->apiUrl, [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => ['data' => [[
                'Deal_Name' => $data['name'],
                'Stage' => $data['stage'],
                'Closing_Date' => $data['closing_date'],
                'Account_Name' => ['id' => $accountId],
            ]]],
        ]);
    }
}
