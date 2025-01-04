<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubCategoryController;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);

Route::post('/categories',[CategoryController::class,'store']);

Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

Route::get('/subcategories', [SubCategoryController::class, 'index']);
Route::post('/subcategories',[SubCategoryController::class,'store']);

Route::get('/subcategories/{id}', [SubCategoryController::class, 'show']);
Route::put('/subcategories/{id}', [SubCategoryController::class, 'update']);
Route::delete('/subcategories/{id}', [SubCategoryController::class, 'destroy']);

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/make-payment', [PaymentController::class, 'makePayment'] );

Route::get('/payment-success', function () {
    return view('payment.success');
})->name('payment.success');

Route::get('/payment-cancel', function () {
    return view('payment.cancel');
})->name('payment.cancel');
