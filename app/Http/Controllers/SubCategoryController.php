<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubCategoryController extends Controller
{
    public function index()
    {
        return SubCategory::with('category')->latest()->get();
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',  // Ensure category exists in the categories table
                'sub_category_name' => ['required', 'string', 'unique:sub_categories,name'],
            ]);

            $subCategoryExist = SubCategory::where('category_id', $validated['category_id'])
                ->where('name', $validated['sub_category_name'])
                ->exists();

            if ($subCategoryExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub Category already exists',
                ]);
            }

            SubCategory::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['sub_category_name'],
                'slug' => Str::slug($validated['sub_category_name']),
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Sub Category created successfully',
            ]);
        } catch (ValidationException $e) {

            $array = $e->errors();

            return response()->json([
                'success' => false,
                'message' => reset($array)[0],
            ], 422);
        }


    }

    public function show($id)
    {
        return SubCategory::with('category')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {

        try {
            $validated = $request->validate([
                'category_id' => ['required', 'exists:sub_categories,category_id'],
                'sub_category_name' => ['required', 'string', 'min:3', 'unique:sub_categories,name'],
            ]);

            $subCategory = SubCategory::findOrFail($id);


            $subCategory->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['sub_category_name'],
                'slug' => Str::slug($validated['sub_category_name']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sub category updated successfully'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }


    }

    public function destroy($id) {
        $deletedId = SubCategory::destroy($id);

        if ($deletedId){
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        }
    }



}
