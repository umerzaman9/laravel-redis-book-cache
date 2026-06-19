<?php

use App\Http\Controllers\Auth\AuthController;
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

Route::post('/register/send-otp', [AuthController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');

Route::middleware(['auth'])->group(function () {
    // Homepage - View all books
    Route::get('/', [BookController::class, 'index'])->name('books.index');

    // Show the creation form
    Route::get('/create', [BookController::class, 'create'])->name('books.create');

    // Handle form submission
    Route::post('/create', [BookController::class, 'store'])->name('books.store');
});
