<?php

use App\Http\Controllers\PaymentController;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/products', function (Request $request) {

    $category_param = $request->query('category'); // Get the 'category' query parameter
    $subcategorySlugs = $request->query('subcategory'); // Get the 'subcategory' query parameter
    $perPage = $request->query('per_page', 10);

    $category = Category::query()->where('slug', $category_param)->first();

    // Example filtering logic
    $query = Product::query();

    if(is_array($subcategorySlugs)) {
        $subcategory = SubCategory::whereIn('slug', $subcategorySlugs)->pluck('id');

        if($subcategory->isNotEmpty()) {
            $query->whereIn('sub_category_id', $subcategory);
        }
    }



    if ($category) {
        $query->where('category_id', $category->id);
    }




    $products = $query->simplePaginate(15);



    $categories = Category::with('subcategories')->get();


    return response()->json([
        'products' => $products,
        'categories' => $categories,
        'subcategories' => 'Subcategories',
    ]);

});

Route::get('/categories', function () {
    return response()->json(Category::with('subcategories')->get());
});

Route::get('/products/{id}', function ($id) {
    return Product::find($id);
});

Route::post('/make-payment', [PaymentController::class, 'makePayment'] );

Route::get('/payment-success', function () {
    return view('payment.success');
})->name('payment.success');

Route::get('/payment-cancel', function () {
    return view('payment.cancel');
})->name('payment.cancel');
