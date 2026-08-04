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
    public function createDeal(array $data)
    {
        $account = $this->accountService->create(data_get($data, 'account'));
        $deal = $this->dealService->create(data_get($data, 'deal'), $account->id);
    }
}
