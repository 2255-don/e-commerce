<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Display the public marketplace.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'seller'])
            ->where('stock_quantity', '>', 0); // Only show available products

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price Filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->paginate(12);
        
        // Load categories for filter sidebar
        $categories = Category::orderBy('name', 'asc')->get();

        return view('pages.marketplace.index', compact('products', 'categories'));
    }

    /**
     * Show product details (Public).
     */
    public function show(Product $product)
    {
        // Using same view logic as seller preview but for public
        // Or create a dedicated public view. Let's create `pages.marketplace.show`
        return view('pages.marketplace.show', compact('product'));
    }
}
