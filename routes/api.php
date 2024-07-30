<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\BankingDetailController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register_user', [UserController::class, 'store']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/verify_two_factor_token', [UserController::class, 'verifyTwoAuth']);
Route::post('/resend_two_factor_token/{email}', [UserController::class, 'resendTwoFactorCode']);
Route::post('/email/resend-verification/{email}', [UserController::class, 'resendVerificationEmail']);
Route::post('/forgot_password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset_password/{token}', [PasswordResetController::class, 'resetPassword']);
// Route for logging out the user
Route::post('/logout_user', [UserController::class, 'logoutUser']);

Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {

    Route::get('/login_user_data', [UserController::class, 'show']);
    Route::post('/user_profile', [UserController::class, 'userProfile']);
    // Route for logging out the user
    Route::post('/logout_user', [UserController::class, 'logoutUser']);

    Route::post('/add_customer', [CustomerController::class, 'store']);
    Route::get('/customers_list', [CustomerController::class, 'index']);
    Route::get('/customer_data/{customerID}', [CustomerController::class, 'show']);
    Route::post('/update_customer', [CustomerController::class, 'update']);
    Route::post('/delete_customer/{customerID}', [CustomerController::class, 'destroy']);

    Route::get('/banks_list', [BankingDetailController::class, 'index']);
    Route::post('/add_bank', [BankingDetailController::class, 'store']);
    Route::get('/bank_data/{bankID}', [BankingDetailController::class, 'show']);
    Route::post('/update_bank/{bankID}', [BankingDetailController::class, 'update']);
    Route::post('/delete_bank/{bankID}', [BankingDetailController::class, 'destroy']);

    Route::get('/invoices_list', [InvoiceController::class, 'index']);
    Route::post('/add_invoice', [InvoiceController::class, 'store']);
    Route::get('/invoice_data/{InvoiceID}', [InvoiceController::class, 'show']);
    Route::post('/update_invoice/{InvoiceID}', [InvoiceController::class, 'update']);
    Route::post('/delete_invoice/{InvoiceID}', [InvoiceController::class, 'destroy']);

    Route::get('/payments_list/{invoiceID}', [PaymentsController::class, 'index']);
    Route::post('/add_payment', [PaymentsController::class, 'store']);
    Route::get('/payment_data/{paymentID}', [PaymentsController::class, 'show']);
    Route::post('/update_payment/{paymentID}', [PaymentsController::class, 'update']);
    Route::post('/delete_payment/{paymentID}', [PaymentsController::class, 'destroy']);

    Route::get('/dashboard_data', [DashboardController::class, 'index']);
    Route::get('/recent_invoices', [DashboardController::class, 'getRecentInvoices']);
});