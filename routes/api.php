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

Route::post('/tasks', [ImageController::class, 'upload'])->name('upload');
Route::get('/tasks/{uuid}', [ImageController::class, 'info'])->name('info');
Route::get('/files/{path}', [ImageController::class, 'download'])->name('download')->where('path', '.*');
