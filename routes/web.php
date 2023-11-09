<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
// ->middleware('password.confirm');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard')->middleware('verified');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('book')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('book');
    Route::get('/detail/{id}', [BookController::class, 'detail'])->name('book.detail');
    Route::get('/edit/{id}', [BookController::class, 'edit'])->name('book.edit');
    Route::get('/new', [BookController::class, 'new'])->name('book.new');

    Route::patch('/update', [BookController::class, 'update'])->name('book.update');
    Route::post('/create', [BookController::class, 'create'])->name('book.create');
    Route::delete('/remove/{id}', [BookController::class, 'remove'])->name('book.remove');

        // get: 取得
    // post: データの新規作成
    // patch: データの一部変更
    // put: データを一括変更
    // ※ 基本的にpatchとputは大して違いがない。どちらを使っても何も変わらない。
    // delete: データの削除
});

require __DIR__.'/auth.php';
