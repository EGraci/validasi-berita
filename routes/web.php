<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NlpController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [NlpController::class, 'index']);
Route::post('/cek/', [NlpController::class, 'cek']);
Route::get('/hasil/', [NlpController::class, 'cek']);
Route::get('/hoax/', [NlpController::class, 'hoax']);
Route::post('/hoax/', [NlpController::class, 'simpan']);


