<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Deal\CreateRequest;
use App\Services\Zoho\DealCreationService;

class DealsController extends Controller
{
    public function __construct(
        private readonly DealCreationService $dealCreationService
    ) {}

    public function create(CreateRequest $request) {
        $deal = $this->dealCreationService->createDeal($request->validated());
    }
}
