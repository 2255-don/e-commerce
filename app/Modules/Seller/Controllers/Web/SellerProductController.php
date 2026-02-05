<?php

namespace Modules\Seller\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Marketplace\Repositories\ProductRepository;
use Modules\Marketplace\DTOs\CreateProductDTO;
use Modules\Marketplace\DTOs\UpdateProductDTO;
use Modules\Marketplace\Services\StockService;
use Illuminate\Http\Request;

class SellerProductController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly StockService $stockService,
    ) {}
    
    public function index()
    {
        $products = $this->productRepository->getBySeller(auth()->id(), 15);
        return view('pages.seller.products.index', compact('products'));
    }
    
    public function create()
    {
        return view('pages.seller.products.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'nullable|string|exists:categories,id',
        ]);
        
        $validated['seller_id'] = auth()->id();
        
        $dto = CreateProductDTO::fromRequest($validated);
        $product = $this->productRepository->create($dto->toArray());
        
        return redirect()->route('seller.products.index')
            ->with('success', 'Product created successfully');
    }
    
    public function edit(string $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product || $product->seller_id !== auth()->id()) {
            abort(404);
        }
        
        return view('pages.seller.products.edit', compact('product'));
    }
    
    public function update(Request $request, string $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product || $product->seller_id !== auth()->id()) {
            abort(404);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
        ]);
        
        $dto = UpdateProductDTO::fromRequest($validated);
        $this->productRepository->update($id, $dto->toArray());
        
        return redirect()->route('seller.products.index')
            ->with('success', 'Product updated successfully');
    }
    
    public function updateStock(Request $request, string $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);
        
        $this->stockService->updateStock($id, $validated['quantity']);
        
        return redirect()->back()->with('success', 'Stock updated');
    }
    
    public function deactivate(string $id)
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product || $product->seller_id !== auth()->id()) {
            abort(404);
        }
        
        $product->deactivate();
        
        return redirect()->back()->with('success', 'Product deactivated');
    }
}
