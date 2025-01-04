<?php

namespace App\Http\Controllers;


use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariants;
use App\Models\VariantImages;
use App\Models\VariantSizes;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category_param = $request->query('category'); // Get the 'category' query parameter
        $subcategorySlugs = $request->query('subcategory'); // Get the 'subcategory' query parameter
        $perPage = $request->query('per_page', 10);

        $category = Category::query()->where('slug', $category_param)->first();

        // Example filtering logic
        $query = Product::query();

        if (is_array($subcategorySlugs)) {
            $subcategory = SubCategory::whereIn('slug', $subcategorySlugs)->pluck('id');

            if ($subcategory->isNotEmpty()) {
                $query->whereIn('sub_category_id', $subcategory);
            }
        }


        if ($category) {
            $query->where('category_id', $category->id);
        }


        $products = $query->with(['category','subCategory']) ->latest()->simplePaginate(15);


        $categories = Category::with('subcategories')->get();


        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'subcategories' => 'Subcategories',
        ]);

    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|exists:categories,id',
                'subcategory' => 'required|exists:sub_categories,id',
                'price' => 'required|numeric|min:0',
                'rating' => 'required|numeric|min:0|max:5',
                'discount' => 'nullable|min:0',
                'discountType' => 'required|in:fixed,percentage,no-discount',
                'colorOption' => 'nullable|in:no-color,color',

                'colorVariants' => 'required|array',
                'colorVariants.*.color' => 'required|string',
                'colorVariants.*.colorName' => 'nullable|string',
                'colorVariants.*.colorOption' => 'required|string',
                'colorVariants.*.price' => 'required|numeric|min:0',
                'colorVariants.*.stock' => 'required|integer|min:0',
                'colorVariants.*.discountType' => 'required|in:fixed,percentage,no-discount',
                'colorVariants.*.discount' => 'nullable|min:0',
                'colorVariants.*.images' => 'required|array',
                'colorVariants.*.images.*' => 'file|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'colorVariants.*.sizes' => 'required|array',
                'colorVariants.*.sizes.*' => 'string',
            ]);

            DB::beginTransaction();

            $savedProduct = Product::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'category_id' => $validated['category'],
                'sub_category_id' => $validated['subcategory'],
                'price' => $validated['price'],
                'discount_type' => $validated['discountType'],
                'discount' => $validated['discount'],
                'rating' => $validated['rating'],
                'color_option' => $validated['colorOption'],
                'img_url' => '',
                'video_url' => ''

            ]);


            foreach ($validated['colorVariants'] as $colorVariant) {
                ProductVariants::create([
                    'product_id' => $savedProduct->id,
                    'color_name' => $colorVariant['colorName'], // Optional
                    'color_value' => $colorVariant['color'],
                    'price' => $colorVariant['price'],
                    'stock' => $colorVariant['stock'],
                    'discount_type' => $colorVariant['discountType'],
                    'discount' => $colorVariant['discount'] ?? 0,
                ]);

                foreach ($colorVariant['sizes'] as $size) {
                    VariantSizes::create([
                        'product_id' => $savedProduct->id,
                        'size' => $size
                    ]);
                }

                foreach ($colorVariant['images'] as $image) {
                    $customFileName = 'product_' . $savedProduct->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $filePath = $image->storeAs('uploads', $customFileName, 'public');

                    VariantImages::create([
                        'product_id' => $savedProduct->id,
                        'url' => $filePath,
                    ]);
                }

            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product Created successfully'
            ]);
        } catch (\Exception $exception) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

    }

    public function show(int $id)
    {

        $product = Product::with([
            'category' => function ($query) {
                $query->select('id', 'name', 'slug');
            },
            'subCategory' => function ($query) {
                $query->select('id', 'name', 'slug');
            },

        ])->findOrFail($id);

        // Manually restructure the product data
        $productData = $product->toArray();


        // Rebuild the variants array with proper formatting
        $productData['product_variants'] = $product->productVariants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'productId' => $variant->product_id,
                'colorName' => $variant->color_name,
                'colorValue' => $variant->color_value,
                'colorOption' => $variant->color_option,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'discountType' => $variant->discount_type,
                'discount' => $variant->discount,
                'sizes' => $variant->sizes->pluck('size'),
                'images' => $variant->images->pluck('url')
            ];
        });

        // Return the formatted response
        return response()->json($productData);

    }
}
