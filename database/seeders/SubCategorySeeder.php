<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductVariants;
use App\Models\VariantImages;
use App\Models\Product;
use App\Models\VariantSizes;
use App\Models\ProductReview;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $jsonFilePath = storage_path('app/furniture.json');


        if (File::exists($jsonFilePath)) {
            // Get the contents of the file
            $jsonData = File::get($jsonFilePath);

            // Decode the JSON data into an array
            $productsData = json_decode($jsonData, true);


            User::factory(20)->create();

            // Loop through each product
            foreach ($productsData as $productData) {

                // Find or create the category
                $category = Category::firstOrCreate([
                    'name' => $productData['category'],
                    'slug' => Str::slug($productData['category']),
                ]);

                // Find or create the subcategory
                $subCategory = SubCategory::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $productData['sub_category'],
                    'slug' => Str::slug($productData['sub_category']),
                ]);

                // Create the product
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'category_id' => $category->id,
                    'sub_category_id' => $subCategory->id,
                    'price' => $productData['price'],
                    'img_url' => $productData['img_url'],
                    'description' => $productData['description'],
                    'video_url' => $productData['video_url'],
                    'rating' => $productData['rating'],
                    'color_option' => 'color',
                    'discount_type' => 'no-discount',
                    'discount' => 0,

                ]);

                for ($i = 0; $i < rand(6, 26); $i++) {
                    ProductReview::create([
                        'product_id' => $product->id,
                        'rating' => mt_rand(350, 500) / 100,
                        'review' => fake()->paragraph(50),
                        'user_id' => rand(1, 10),

                    ]);
                }


                foreach ($productData['variants'] as $variant) {


                    $productVariant = ProductVariants::create([
                        'product_id' => $product->id,
                        'color_value' => $variant['color_value'],
                        'color_name' => $variant['color_name'],
                        'color_option'=> 'color',
                        'price' => $variant['price'],
                        'discount_type' => $variant['discount_type'],
                        'discount' => $variant['discount'],
                        'stock' => $variant['stock'],

                    ]);

                    foreach ($variant['images'] as $image) {
                        VariantImages::create([
                            'product_id' => $product->id,
                            'product_variants_id' => $productVariant->id,
                            'url' => $image,
                        ]);
                    };

                    foreach ($variant['sizes'] as $size) {
                        VariantSizes::create([
                            'product_id' => $product->id,
                            'product_variants_id' => $productVariant->id,
                            'size' => $size,
                        ]);
                    }


                }

            }


        } else {
            echo "The JSON file does not exist.";
        }


    }
}
