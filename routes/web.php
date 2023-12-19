<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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

Route::get('/', UserController::class)->name('home');

Route::controller(ActionController::class)->group(function () {
    Route::get('/actions', 'page')->name('actions');
    Route::post('/actions-add', 'addAction')->name('actions.add');
    Route::post('/actions-subtract', 'subtractAction')->name('actions.subtract');
    Route::post('/actions-transfer', 'transferAction')->name('actions.transfer');
});

Route::controller(TransactionController::class)->group(function () {
    Route::get('/transactions', 'page')->name('transactions');
    Route::get('/transactions/{transaction}/approve', 'approveAction')->name('transactions.approve');
    Route::get('/transactions/{transaction}/refund', 'refundAction')->name('transactions.refund');
});

Route::get('/logs', LogController::class)->name('logs');
