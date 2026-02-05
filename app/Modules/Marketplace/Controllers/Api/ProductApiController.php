<?php

namespace Modules\Marketplace\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Repositories\ProductRepository;
use Modules\Marketplace\DTOs\ProductFilterDTO;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {}
    
    /**
     * Get paginated product list
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $products = $this->productRepository->search([], $perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
    
    /**
     * Get single product details
     */
    public function show(string $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }
    
    /**
     * Search products with filters
     */
    public function search(Request $request)
    {
        $filters = [
            'search' => $request->input('q'),
            'category_id' => $request->input('category_id'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'seller_id' => $request->input('seller_id'),
        ];
        
        $perPage = $request->input('per_page', 15);
        $products = $this->productRepository->search($filters, $perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'filters' => $filters,
        ]);
    }
}
