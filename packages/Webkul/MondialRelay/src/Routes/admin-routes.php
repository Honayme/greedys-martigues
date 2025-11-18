<?php

use Illuminate\Support\Facades\Route;
use Webkul\MondialRelay\Http\Controllers\Admin\LabelController;

Route::group([
    'middleware' => ['web', 'admin'],
    'prefix'     => config('app.admin_url'),
], function () {
    Route::prefix('mondial-relay')->group(function () {
        Route::post('orders/{id}/generate-label', [LabelController::class, 'generate'])
            ->name('admin.mondialrelay.generate_label');

        Route::get('orders/{id}/download-label', [LabelController::class, 'download'])
            ->name('admin.mondialrelay.download_label');
    });
});
