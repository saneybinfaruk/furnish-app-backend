<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductReview;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                ]);

                for($i = 0; $i < rand(6,26); $i++) {
                    ProductReview::create([
                        'product_id' => $product->id,
                        'rating' => mt_rand(350,500) / 100,
                        'review' => fake()->paragraph(50),
                        'user_id' => rand(1,10),

                    ]);
                }

                // Insert other images
                foreach ($productData['other_img_urls'] as $imgUrl) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $imgUrl,
                    ]);
                }

                // Insert color options
                foreach ($productData['colors'] as $color) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_value' => $color['color_value'],
                        'price' => $color['price'],
                    ]);
                }

                // Insert size options
                foreach ($productData['sizes'] as $size) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size' => $size,
                    ]);
                }
            }


        } else {
            echo "The JSON file does not exist.";
        }



    }
}
