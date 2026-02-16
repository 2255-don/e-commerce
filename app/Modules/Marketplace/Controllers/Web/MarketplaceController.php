<?php

namespace Modules\Marketplace\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Repositories\ProductRepository;
use Modules\Marketplace\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {}
    
    public function index()
    {
        $products = $this->productRepository->search([], 12);
        $categories = $this->categoryRepository->getRootCategories();
        
        return view('marketplace::index', compact('products', 'categories'));
    }
    
    public function show(string $slug)
    {
        $product = $this->productRepository->findBySlug($slug);
        
        if (!$product) {
            abort(404, 'Product not found');
        }
        
        return view('marketplace::show', compact('product'));
    }
    
    public function search(Request $request)
    {
        $filters = [
            'search' => $request->input('q'),
            'category_id' => $request->input('category'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
        ];
        
        $products = $this->productRepository->search($filters, 12);
        $categories = $this->categoryRepository->getRootCategories();
        
        return view('marketplace::index', compact('products', 'categories'));
    }
}
