<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::any('madeinchina', [PaController::class, 'index']);
Route::any('alibaba', [PaController::class, 'alibaba']);
Route::any('drill', [PaController::class, 'drill']);