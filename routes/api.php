<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::post('/register', [TransactionController::class, 'register']);
Route::post('/login', [TransactionController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User management
    Route::post('/logout', [TransactionController::class, 'logout']);

    // Application management
    Route::post('/application', [TransactionController::class, 'submitApplication']);
    Route::get('/application/status', [TransactionController::class, 'getApplicationStatus']);

    // Transaction management
    Route::post('/transaction', [TransactionController::class, 'processTransaction']);
    Route::get('/transactions', [TransactionController::class, 'getTransactionHistory']);

    // Feedback management
    Route::post('/feedback', [TransactionController::class, 'submitFeedback']);
    Route::get('/vendors/feedback', [TransactionController::class, 'getVendorsForFeedback']);

    // User profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
