<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\VendorProfileController;
use App\Http\Controllers\VendorServiceController;
use App\Http\Controllers\VendorQrCodeController;
use App\Http\Controllers\VendorTransactionController;
use App\Http\Controllers\VendorFeedbackController;
use App\Http\Controllers\VendorSettingsController;
use App\Models\Application;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

// Authenticated Routes
Route::group(['middleware' => 'auth'], function () {

	Route::get('/', [HomeController::class, 'home']);

	// Admin Routes - Protected by admin middleware
	Route::group(['middleware' => 'admin'], function () {

		Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

		// Student Routes
		Route::resource('student', StudentController::class);

		// Vendor Routes (Admin managing vendors)
		Route::resource('vendor', VendorController::class);

		// Application Routes
		Route::resource('application', ApplicationController::class);
		Route::get('application/{application}/download', [ApplicationController::class, 'downloadDocument'])
			->name('application.downloadDocument');
		Route::post('application/bulk-approve', [ApplicationController::class, 'bulkApprove'])
			->name('application.bulkApprove');

		// QR Code Routes
		Route::get('qrcode/vendor/{vendor}/services', [QrCodeController::class, 'getVendorServices'])
			->name('qrcode.vendor.services');
		Route::post('qrcode/bulk-expire', [QrCodeController::class, 'bulkExpire'])
			->name('qrcode.bulk-expire');
		Route::resource('qrcode', QrCodeController::class);
		Route::get('qrcode/{qrCode}/download', [QrCodeController::class, 'download'])
			->name('qrcode.download');
		Route::get('qrcode/{qrCode}/image', [QrCodeController::class, 'image'])
			->name('qrcode.image');

		// Service Routes
		Route::get('services/create/{vendor?}', [ServiceController::class, 'create'])->name('services.create');
		Route::resource('services', ServiceController::class)->except(['create']);

		// Report Routes
		Route::get('report', [ReportController::class, 'showReportOptions'])->name('report.index');
		Route::get('report/dashboard', [ReportController::class, 'index'])->name('report.dashboard');
		Route::get('report/participation', [ReportController::class, 'showStudentParticipation'])->name('report.participation');
		Route::get('report/financial', [ReportController::class, 'showFinancial'])->name('report.financial');
		Route::get('report/financial/{vendor}', [ReportController::class, 'showFinancial'])->name('report.financial.vendor');
		Route::get('report/anomaly', [ReportController::class, 'showAnomaly'])->name('report.anomaly');
		Route::get('report/feedback', [ReportController::class, 'showFeedback'])->name('report.feedback');
		Route::get('report/feedback/{vendor}', [ReportController::class, 'showFeedback'])->name('report.feedback.vendor');
	});

	// CHANGED: Vendor Routes - Now using 'vendorside' prefix
	Route::group(['middleware' => 'vendor', 'prefix' => 'vendorside', 'as' => 'vendor.'], function () {

		// Dashboard
		Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

		// Profile Management
		Route::get('/profile', [VendorProfileController::class, 'index'])->name('profile');
		Route::get('/profile/edit', [VendorProfileController::class, 'edit'])->name('profile.edit');
		Route::put('/profile', [VendorProfileController::class, 'update'])->name('profile.update');

		// Services Management
		Route::resource('services', VendorServiceController::class);
		Route::post('services/{service}/toggle-availability', [VendorServiceController::class, 'toggleAvailability'])
			->name('services.toggle-availability');

		// QR Codes Management
		Route::resource('qrcodes', VendorQrCodeController::class);
		Route::get('qrcodes/{qrcode}/download', [VendorQrCodeController::class, 'download'])
			->name('qrcodes.download');
		Route::get('qrcodes/{qrcode}/image', [VendorQrCodeController::class, 'image'])
			->name('qrcodes.image');
		Route::post('qrcodes/bulk-expire', [VendorQrCodeController::class, 'bulkExpire'])
			->name('qrcodes.bulk-expire');

		// Transactions
		Route::get('/transactions', [VendorTransactionController::class, 'index'])
			->name('transactions.index');
		Route::get('/transactions/{transaction}', [VendorTransactionController::class, 'show'])
			->name('transactions.show');
		Route::get('/transactions-export', [VendorTransactionController::class, 'export'])
			->name('transactions.export');

		// Feedback & Reviews
		Route::get('/feedback', [VendorFeedbackController::class, 'index'])
			->name('feedback.index');
		Route::get('/feedback/{rating}/respond', [VendorFeedbackController::class, 'respond'])
			->name('feedback.respond');
		Route::post('/feedback/{rating}/response', [VendorFeedbackController::class, 'storeResponse'])
			->name('feedback.store-response');
		Route::put('/feedback/{rating}/response', [VendorFeedbackController::class, 'updateResponse'])
			->name('feedback.update-response');
		Route::delete('/feedback/{rating}/response', [VendorFeedbackController::class, 'deleteResponse'])
			->name('feedback.delete-response');
		Route::get('/feedback-export', [VendorFeedbackController::class, 'export'])
			->name('feedback.export');

		// Settings
		Route::get('/settings', [VendorSettingsController::class, 'index'])->name('settings');
		Route::put('/settings', [VendorSettingsController::class, 'update'])->name('settings.update');
	});

	// General Routes for all authenticated users
	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	// User Profiles
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);

	Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	Route::get('/logout', [SessionsController::class, 'destroy']);
});

// Guest Routes
Route::group(['middleware' => 'guest'], function () {
	Route::get('/register', [RegisterController::class, 'create']);
	Route::post('/register', [RegisterController::class, 'store']);
	Route::get('/login', [SessionsController::class, 'create']);
	Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

// Login route (accessible by both guest and auth for redirect purposes)
Route::get('/login', function () {
	return view('session/login-session');
})->name('login');

// Test route for debugging time zones
Route::get('/test-time', function () {
	return [
		'app_timezone' => config('app.timezone'),
		'db_timezone' => config('database.connections.mysql.timezone'),
		'php_timezone' => ini_get('date.timezone'),
		'current_time' => now(),
	];
});
