<?php

use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/sitemap.xml', function () {
    $path = Storage::disk('public')->path('sitemap.xml');

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, ['Content-Type' => 'text/xml']);
});