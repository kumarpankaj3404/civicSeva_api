<?php

use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AssistantController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\EligibilityController;
use App\Http\Controllers\Api\V1\SchemeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CivicConnect API v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ─── Public: Auth ──────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register', RegisterController::class)->name('auth.register');
        Route::post('login',    [LoginController::class, 'login'])->name('auth.login');
    });

    // ─── Public: Schemes & Categories ─────────────────────────────────────────
    Route::get('schemes',                [SchemeController::class,  'index'])->name('schemes.index');
    Route::get('schemes/{id}',           [SchemeController::class,  'show'])->name('schemes.show');
    Route::get('categories',             [CategoryController::class, 'index'])->name('categories.index');

    // ─── Public: Eligibility ───────────────────────────────────────────────────
    Route::post('eligibility/check',     [EligibilityController::class, 'check'])->name('eligibility.check');
    Route::post('eligibility/bulk-check',[EligibilityController::class, 'bulkCheck'])->name('eligibility.bulk-check');

    // ─── Protected: Requires Sanctum Token ────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('auth/logout',       [LoginController::class,  'logout'])->name('auth.logout');
        Route::get('auth/me',            [ProfileController::class,'me'])->name('auth.me');
        Route::put('auth/profile',       [ProfileController::class,'update'])->name('auth.profile.update');

        // Conversations (Chat History)
        Route::prefix('assistant')->group(function () {
            Route::get('conversations',                           [AssistantController::class, 'listConversations'])->name('assistant.conversations.index');
            Route::post('conversations',                          [AssistantController::class, 'createConversation'])->name('assistant.conversations.create');
            Route::get('conversations/{id}',                      [AssistantController::class, 'showConversation'])->name('assistant.conversations.show');
            Route::delete('conversations/{id}',                   [AssistantController::class, 'archiveConversation'])->name('assistant.conversations.archive');
            Route::patch('conversations/{id}/progress',           [AssistantController::class, 'updateProgress'])->name('assistant.conversations.progress');
            Route::post('conversations/{id}/messages',            [AssistantController::class, 'storeMessages'])->name('assistant.conversations.messages.store');
        });

        // Applications
        Route::get('applications',                               [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{id}',                          [ApplicationController::class, 'show'])->name('applications.show');
        Route::post('applications/submit',                       [ApplicationController::class, 'submit'])->name('applications.submit');

        // ─── Admin Only ────────────────────────────────────────────────────────
        Route::middleware('role:admin')->group(function () {
            // Scheme management
            Route::post('schemes',               [SchemeController::class,  'store'])->name('schemes.store');
            Route::put('schemes/{id}',           [SchemeController::class,  'update'])->name('schemes.update');
            Route::delete('schemes/{id}',        [SchemeController::class,  'destroy'])->name('schemes.destroy');

            // Category management
            Route::post('categories',            [CategoryController::class, 'store'])->name('categories.store');
            Route::delete('categories/{id}',     [CategoryController::class, 'destroy'])->name('categories.destroy');

            // Application status management
            Route::patch('applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
        });

    });

});
