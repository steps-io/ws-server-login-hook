<?php
use Illuminate\Support\Facades\Route;
use Steps\WsLoginHook\Http\Controllers\WhatsappAuthHookController;

Route::name('api.ws.login.hook.')
    ->prefix('api/ws-login-hook')
    ->middleware('api')
    ->group(function () {

        Route::post('request-otp/{model}', [WhatsappAuthHookController::class, 'requestOtp'])->name('request.otp');
        Route::any('ws-webhook', [WhatsappAuthHookController::class, 'wsWebhook'])->name('ws.webhook');
        Route::post('hook-otp-login', [WhatsappAuthHookController::class, 'login'])->name('login');
    });