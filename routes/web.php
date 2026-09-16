<?php

use Illuminate\Support\Facades\Route;
use Vendor\LaravelMeta\Http\Controllers\SandboxController;

Route::prefix(config('meta.sandbox.route', '/meta/sandbox'))
    ->middleware(['web'])
    ->group(function () {
        Route::get('/', [SandboxController::class, 'index'])->name('meta.sandbox.index');
        Route::post('/test-token', [SandboxController::class, 'testToken'])->name('meta.sandbox.test-token');
        Route::post('/get-lead', [SandboxController::class, 'getLead'])->name('meta.sandbox.get-lead');
        Route::post('/publish-facebook', [SandboxController::class, 'publishFacebook'])->name('meta.sandbox.publish-facebook');
    });
