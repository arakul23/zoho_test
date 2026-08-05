<?php

declare(strict_types=1);

namespace App\Services\Zoho;

use GuzzleHttp\Client;

readonly class AccountService
{
    private const string MODULE_NAME = 'Accounts';

    private string $apiUrl;

    private string $token;

    public function __construct(private Client $client, private TokenService $tokenService)
    {
        $this->apiUrl = sprintf('%s/%s', config('services.zoho.api_url'), self::MODULE_NAME);
        $this->token = $this->tokenService->getAccessToken();
    }

    public function create(array $data): array
    {
        $response = $this->client->post($this->apiUrl, [
            'headers' => [
                'Authorization' => 'Zoho-oauthtoken ' . $this->token,
                'Content-Type' => 'application/json',
            ],
            'json' => ['data' => [[
                'Account_Name' => $data['name'],
                'Website' => $data['website'],
                'Phone' => $data['phone'],
            ]]],
        ]);

        $responseArray = json_decode($response->getBody()->getContents(), true);

        return data_get($responseArray, 'data.0');
    }
}
