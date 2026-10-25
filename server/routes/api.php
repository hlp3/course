<?php

use App\Http\Controllers\FactController;
use Illuminate\Support\Facades\Route;

Route::apiResource('facts', FactController::class)
    ->only(['index', 'show', 'store']);