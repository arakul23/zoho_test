<?php

declare(strict_types=1);

use App\Http\Controllers\DealsController;
use Illuminate\Support\Facades\Route;

Route::post('/zoho/deals', [DealsController::class, 'create'])->name('deals.create');
