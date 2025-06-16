<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
   /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::orderBy('created_at', 'desc')->get();
            
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|min:2|max:255',
                'title' => 'required|string|min:2|max:255',
                'description' => 'required|string|min:10|max:1000',
                'price' => 'required|numeric|min:0.01',
                'available_quantity' => 'required|integer|min:0'
            ]);

            $product = Product::create($validatedData);

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            
            return response()->json($product, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|min:2|max:255',
                'title' => 'required|string|min:2|max:255',
                'description' => 'required|string|min:10|max:1000',
                'price' => 'required|numeric|min:0.01',
                'available_quantity' => 'required|integer|min:0'
            ]);

            $product->update($validatedData);

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products by name, title, or description.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('title', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to search products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
