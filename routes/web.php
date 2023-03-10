<?php

use App\Http\Controllers\AutomaticPickupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PickupController;
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

$controller_path = 'App\Http\Controllers';

// Main Page Route
Route::get('/', [PickupController::class,'view']);
Route::get('/page-2', $controller_path . '\pages\Page2@index')->name('pages-page-2');

// pages
Route::get('/pages/misc-error', $controller_path . '\pages\MiscError@index')->name('pages-misc-error');

// authentication
Route::get('/auth/login-basic', $controller_path . '\authentications\LoginBasic@index')->name('auth-login-basic');
Route::get('/auth/register-basic', $controller_path . '\authentications\RegisterBasic@index')->name('auth-register-basic');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/pickup-list-view', [PickupController::class,'view']);
Route::get('/pickup-report-view', [PickupController::class,'reportView']);
Route::get('/pickup-report', [PickupController::class,'report']);
Route::get('/pickup-list', [PickupController::class,'index']);
Route::get('/pickup-create', [PickupController::class,'create']);
Route::get('/pickup-driver-view', [PickupController::class,'driverView']);
Route::get('/pickup-driver', [PickupController::class,'driverIndex']);
Route::get('/pickup-edit/{pickup}', [PickupController::class,'edit']);

Route::post('/pickup-create', [PickupController::class,'store']);
Route::post('/pickup-edit/{pickup}', [PickupController::class,'update']);
Route::post('/pickup-notification', [PickupController::class,'notification']);
Route::post('/pickup-complete/{pickup}', [PickupController::class,'complete']);
Route::delete('/pickup-delete/{pickup}', [PickupController::class,'destroy']);

Route::get('/automatic-pickup-create', [AutomaticPickupController::class,'create']);
Route::post('/automatic-pickup-create', [AutomaticPickupController::class,'store']);
Route::post('/automatic-pickup-deploy', [AutomaticPickupController::class,'deploy']);
Route::delete('/automatic-pickup-delete/{pickup}', [AutomaticPickupController::class,'destroy']);

Route::get('/user-view/{user}', [UserController::class,'view']);
Route::get('/user-avatar/{user}', [UserController::class,'avatar']);
Route::post('/user/{user}', [UserController::class,'update']);



