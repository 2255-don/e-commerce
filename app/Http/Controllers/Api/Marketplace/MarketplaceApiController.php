<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class MarketplaceApiController extends Controller
{
    /**
     * List Products
     */
    public function index(Request $request)
    {
        $query = Product::with('seller', 'category')
            ->where('status', 'active'); // Assuming there's a status column

        // Optional: Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Optional: Category Filter
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(20);

        return response()->json($products);
    }

    /**
     * Show Product Details
     */
    public function show($id)
    {
        $product = Product::with('seller', 'category', 'images')->findOrFail($id);
        
        // Add related products logic if needed later
        
        return response()->json($product);
    }
}
