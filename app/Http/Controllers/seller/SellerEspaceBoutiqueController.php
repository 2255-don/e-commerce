<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class SellerEspaceBoutiqueController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display the Seller Dashboard (Espace Boutique).
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $seller = $user->sellerProfile;

            $products = Product::where('seller_id', $user->id)
                ->with(['category', 'images'])
                ->latest()
                ->get();

            // Génération du logo automatique via UI Avatars
            $shopLogoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($seller->shop_name) . '&background=random&color=fff&size=128';

            return view('pages.seller.espace_boutique', compact('seller', 'products', 'shopLogoUrl'));
        } catch (Exception $e) {
            Log::error('Seller Dashboard Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors du chargement de votre boutique.']);
        }
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        try {
            $categories = Category::all();
            return view('pages.seller.product_form', compact('categories'));
        } catch (Exception $e) {
            Log::error('Product Create Form Error: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:physical_good,service',
            'description' => 'nullable|string',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        try {
            $seller = Auth::user()->sellerProfile;
            
            $this->productService->createProduct(
                $seller, 
                $request->all(),
                $request->file('product_images') ?? []
            );

            return redirect()->route('seller.dashboard')->with('status', 'product-created');

        } catch (Exception $e) {
            Log::error('Product Store Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création du produit.']);
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        // Security check: Ensure product belongs to logged in seller
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        return view('pages.seller.product_form', compact('product', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }
        
        return view('pages.seller.product_show', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Security check
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:physical_good,service',
            'description' => 'nullable|string',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        try {
            $this->productService->updateProduct(
                $product,
                $request->all(),
                $request->file('product_images') ?? []
            );

            return redirect()->route('seller.dashboard')->with('status', 'product-updated');

        } catch (Exception $e) {
            Log::error('Product Update Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour.']);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->seller_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->productService->deleteProduct($product);
            return back()->with('status', 'product-deleted');
        } catch (Exception $e) {
            Log::error('Product Delete Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la suppression.']);
        }
        }

    /**
     * Remove the specified product image.
     */
    public function destroyImage(Request $request, $productImageId)
    {
        try {
            // Need to find image manually since we passed ID string/UUID
            $image = \App\Models\ProductImage::findOrFail($productImageId);
            $product = $image->product;

            // Security check
            if ($product->seller_id !== Auth::id()) {
                abort(403);
            }

            // Delete file from storage
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            return back()->with('status', 'image-deleted');
        } catch (Exception $e) {
            Log::error('Image Delete Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'image.']);
        }
    }
}
