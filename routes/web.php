<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

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

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::post('/news/{news}/like', [NewsController::class, 'like'])->name('news.like');
Route::post('/news/{news}/dislike', [NewsController::class, 'dislike'])->name('news.dislike');
Route::get('/parse-news', [NewsController::class, 'parseNews'])->name('news.parse');

