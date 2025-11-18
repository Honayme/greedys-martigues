<?php

use Illuminate\Support\Facades\Route;
use Webkul\MondialRelay\Http\Controllers\PointRelaisController;
use Webkul\MondialRelay\Http\Controllers\CheckoutController;
use Webkul\MondialRelay\Http\Controllers\Admin\LabelController;

// Routes frontend
Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('mondialrelay/search-points', [PointRelaisController::class, 'search'])
        ->name('mondialrelay.search');

    Route::post('mondialrelay/save-point', [CheckoutController::class, 'savePointRelais'])
        ->name('mondialrelay.save_point');
});

// Routes admin
Route::group([
    'prefix' => config('app.admin_url'),
    'middleware' => ['web', 'admin']
], function () {
    Route::post('mondial-relay/orders/{id}/generate-label', [LabelController::class, 'generate'])
        ->name('admin.mondialrelay.generate_label');

    Route::get('mondial-relay/orders/{id}/download-label', [LabelController::class, 'download'])
        ->name('admin.mondialrelay.download_label');
});
