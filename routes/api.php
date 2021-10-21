<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/tasks', [ImageController::class, 'upload']);

Route::get('/tasks/{uuid}', function ($uuid) {
    return ImageController::getById($uuid);
});
Route::get('/files/{path}', function ($uuid) {
    return ImageController::getById($uuid);
});
