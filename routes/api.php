<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WebhookController;

Route::post('/register', [AuthController::class, 'register']);
// routes/web.php
Route::post('login', [AuthController::class, 'login'])->name('login');




Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);

    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/cancel-subscription/{id}', [SubscriptionController::class, 'cancelSubscription']);
    Route::get('/subscriptions', [SubscriptionController::class, 'listSubscriptions']);
    Route::get('/admin/subscriptions', [SubscriptionController::class, 'adminViewSubscriptions']);

    Route::post('/stripe/webhook', [WebhookController::class, 'handleStripeWebhook']);
});
