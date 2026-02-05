<?php

namespace Modules\Seller\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Repositories\ProductRepository;
use Modules\Marketplace\DTOs\CreateProductDTO;
use Modules\Marketplace\DTOs\UpdateProductDTO;
use Modules\Marketplace\Services\StockService;
use Illuminate\Http\Request;

class SellerProductApiController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly StockService $stockService,
    ) {}
    
    /**
     * Get seller's products
     */
    public function index(Request $request)
    {
        $products = $this->productRepository->getBySeller($request->user()->id, 15);
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
    
    /**
     * Create new product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|string|exists:categories,id',
        ]);
        
        try {
            $validated['seller_id'] = $request->user()->id;
            $dto = CreateProductDTO::fromRequest($validated);
            $product = $this->productRepository->create($dto->toArray());
            
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Get product details
     */
    public function show(Request $request, string $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product || $product->seller_id !== $request->user()->id) {
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
     * Update product
     */
    public function update(Request $request, string $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product || $product->seller_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
        ]);
        
        try {
            $dto = UpdateProductDTO::fromRequest($validated);
            $product = $this->productRepository->update($id, $dto->toArray());
            
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Update product stock
     */
    public function updateStock(Request $request, string $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);
        
        try {
            $this->stockService->updateStock($id, $validated['quantity']);
            
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
