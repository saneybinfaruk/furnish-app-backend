<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::with('subcategories')->latest()->get();
    }

    public function store(Request $request)
    {

        $attributes = $request->validate([
            'new_category' => ['required','string','min:3','unique:categories,name'],
        ]);

        $categoryExist = Category::where('name', $attributes)->exists();

        if ($categoryExist) {
            return response()->json([
                'success' => false,
                'message' => 'Category already exists',
            ],);
        }


        Category::create([
            'name' => $attributes['new_category'],
            'slug' => Str::slug($attributes['new_category']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully'
        ]);
    }

    public function show($id)
    {
        return Category::with('subcategories')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {

        try {
            $validatedData = $request->validate([
                'category' => ['required', 'string', 'min:3', 'unique:categories,name'],
            ]);

            $category = Category::findOrFail($id);

            $category->update([
                    'name' => $validatedData['category'],
                    'slug' => Str::slug($validatedData['category'])
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);


        } catch (\Exception $exception) {

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);

        }

    }


    public function destroy($id){
       $deletedId = Category::destroy($id);


       if ($deletedId){
           return response()->json([
               'success' => true,
               'message' => 'Category deleted successfully'
           ]);
       }
    }
}
