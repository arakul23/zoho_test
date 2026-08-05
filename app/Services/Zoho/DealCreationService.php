<?php

declare(strict_types=1);

namespace App\Services\Zoho;

readonly class DealCreationService
{
    public function __construct(
        private DealService    $dealService,
        private AccountService $accountService,
    ) {
    }

    public function createDeal(array $data): array
    {
        $account = $this->accountService->create(data_get($data, 'account'));
        $accountId = (int)data_get($account, 'details.id');

        $deal = $this->dealService->create(data_get($data, 'deal'), $accountId);

        return ['account_id' => $accountId, 'deal_id' => data_get($deal, 'details.id')];
    }
}
