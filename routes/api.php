<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\CompanyNewsController;
use App\Http\Controllers\ServiceItemController;
use App\Http\Controllers\ContactUsMailController;
use App\Http\Controllers\CompanyClientsController;
use App\Http\Controllers\CompanyServiceController;
use App\Http\Controllers\CompanyCertificateController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('company-services')->group(function () {
    Route::get('/', [CompanyServiceController::class, 'index']);
    Route::post('/', [CompanyServiceController::class, 'store']);
    Route::get('/{id}', [CompanyServiceController::class, 'show']);
    Route::post('/{id}', [CompanyServiceController::class, 'update']);
    Route::delete('/{id}', [CompanyServiceController::class, 'destroy']);
});


Route::prefix('contact-us-mails')->group(function () {
    Route::get('/', [ContactUsMailController::class, 'index']);
    Route::post('/', [ContactUsMailController::class, 'store']);
    Route::get('/{id}', [ContactUsMailController::class, 'show']);
    Route::post('/{id}', [ContactUsMailController::class, 'update']);
    Route::delete('/{id}', [ContactUsMailController::class, 'destroy']);
});


Route::prefix('company-news')->group(function () {
    Route::get('/', [CompanyNewsController::class, 'index']);
    Route::post('/', [CompanyNewsController::class, 'store']);
    Route::get('/{id}', [CompanyNewsController::class, 'show']);
    Route::post('/{id}', [CompanyNewsController::class, 'update']);
    Route::delete('/{id}', [CompanyNewsController::class, 'destroy']);
});


Route::prefix('company-clients')->group(function () {
    Route::get('/', [CompanyClientsController::class, 'index']);
    Route::post('/', [CompanyClientsController::class, 'store']);
    Route::get('/{id}', [CompanyClientsController::class, 'show']);
    Route::post('/{id}', [CompanyClientsController::class, 'update']);
    Route::delete('/{id}', [CompanyClientsController::class, 'destroy']);
});


Route::prefix('company-certificates')->group(function () {
    Route::get('/', [CompanyCertificateController::class, 'index']);
    Route::post('/', [CompanyCertificateController::class, 'store']);
    Route::get('showbycode/{id}', [CompanyCertificateController::class, 'show']);
    Route::post('/{id}', [CompanyCertificateController::class, 'update']);
    Route::delete('/{id}', [CompanyCertificateController::class, 'destroy']);
});


Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::group(['prefix' => 'service-items'], function () {
    Route::get('/', [ServiceItemController::class, 'index']);
    Route::post('/', [ServiceItemController::class, 'store']);
    Route::get('/{id}', [ServiceItemController::class, 'show']);
    Route::post('/{id}', [ServiceItemController::class, 'update']);
    Route::delete('/{id}', [ServiceItemController::class, 'destroy']);
});


Route::group(['prefix' => 'statistics'], function () {
    Route::get('/', [StatisticController::class, 'index']);                     
    Route::post('/', [StatisticController::class, 'store']);            
    Route::get('{id}', [StatisticController::class, 'show']);        
    Route::post('{id}', [StatisticController::class, 'update']);      
    Route::delete('{id}', [StatisticController::class, 'destroy']);  
});
