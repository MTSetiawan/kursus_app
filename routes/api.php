<?php

use App\Http\Controllers\AdminPlanRequest;
use App\Http\Controllers\PublicListingController;
use Illuminate\Support\Facades\Route;

Route::get('/public-listing', [PublicListingController::class, 'index']);
Route::get('/public-listing/{region}/{slug}', [PublicListingController::class, 'show']);
Route::get('/public-listings', [PublicListingController::class, 'search']);
