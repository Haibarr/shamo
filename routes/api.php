<?php

use App\Http\Controllers\API\productCategoryController;
use App\Http\Controllers\API\productController;
use App\Http\Controllers\API\transactionController;
use App\Http\Controllers\API\userController;
use App\Models\product;
use App\Models\transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Rules\Role;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user',[userController::class,'fetch']);
    Route::post('user',[userController::class,'updateProfile']);
    Route::post('logout',[userController::class,'logout']);
    Route::get('transactions',[transactionController::class,'all']);
    Route::post('checkout',[transactionController::class,'checkout']);
});


Route::get('products',[productController::class,'all']);

Route::get('categories',[productCategoryController::class,"all"]);

Route::post('register',[userController::class,"register"]);

Route::post('login',[userController::class,'login']);


