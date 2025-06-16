<?php

use App\Http\Controllers\ApiController\TransactionController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ServiceController;
use App\Models\Application;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\QrCodeController;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', function () {
		return view('dashboard');
	})->name('dashboard');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	//Student Routes ---------------------------------------------------------------------------------------------------
	Route::resource('student', StudentController::class);
	
	//Vendor Routes ----------------------------------------------------------------------------------------------------
	Route::resource('vendor', VendorController::class);
	
	//Application Routes -----------------------------------------------------------------------------------------------
	Route::resource('application', ApplicationController::class);

	//QR Code Routes ---------------------------------------------------------------------------------------------------
	Route::resource('qrcode', QrCodeController::class);

	//Service Routes ---------------------------------------------------------------------------------------------------
	Route::get('services/create/{vendor?}', [ServiceController::class, 'create'])->name('services.create');
	// Route::get('services/update/{vendor?}', [ServiceController::class, 'update'])->name('services.update');
	Route::resource('services', ServiceController::class)->except(['create']);

	//Report Routes ---------------------------------------------------------------------------------------------------
	Route::get('report', function(){
		return view('pages.report.report-option');
	});
	Route::get('report/participation', [TransactionController::class, 'showStudentParticipation']);
	Route::get('report/financial', function() {
		$vendors = Vendor::with('user')->get();
        return view('pages.report.report-vendorlist', compact(['vendors']));
	});
	Route::get('report/financial/{vendor}', [TransactionController::class, 'showFinancial']);
	Route::get('report/anomaly', [TransactionController::class, 'showAnomaly']);
	Route::get('report/feedback', function() {
		$vendors = Vendor::with('user')->get();
        return view('pages.report.report-vendorfeedback', compact(['vendors']));
	});
	Route::get('report/feedback/{vendor}', [TransactionController::class, 'showFeedback']);

	//User Profiles ---------------------------------------------------------------------------------------------------
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);

    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	Route::get('/logout', [SessionsController::class, 'destroy']);
});



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

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');

Route::get('/test-time', function() {
    return [
        'app_timezone' => config('app.timezone'),
        'db_timezone' => config('database.connections.mysql.timezone'),
        'php_timezone' => ini_get('date.timezone'),
        'current_time' => now(),
    ];
});