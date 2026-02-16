

@extends('layouts.layoutMaster')

@section('title', $product->title)

@section('content')
<x-feature-section feature="marketplace.view-product-details" showDeniedMessage="true">
<div class="row">
    <!-- Breadcrumb & Wallet -->
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Boutique /</span> Détails Produit
        </h4>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-label-primary p-2 rounded">
                <i class="ti ti-wallet me-1"></i> Solde: {{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', ' ') }} FCFA
            </span>
             <a href="{{ route('marketplace.index') }}" class="btn btn-label-secondary"><i class="ti ti-arrow-left me-1"></i> Retour</a>
        </div>
    </div>

    <!-- Left Column: Product Info -->
    <div class="col-lg-9">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <!-- Images -->
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="text-center mb-3 p-3 bg-lighter rounded">
                            <img src="{{ $product->thumbnail_url }}" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
                        </div>
                        @if($product->images->count() > 1)
                        <div class="d-flex gap-2 overflow-auto">
                            @foreach($product->images as $img)
                                <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank" class="border rounded p-1 hover-shadow">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="rounded" style="width: 70px; height: 70px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="col-md-6">
                        <h6 class="text-muted text-uppercase mb-2">{{ $product->category->name ?? 'Non classé' }}</h6>
                        <h2 class="mb-2 fw-bold">{{ $product->title }}</h2>
                        
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="text-gold mb-0 me-3 fw-bold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</h3>
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success">En Stock ({{ $product->stock_quantity }})</span>
                            @else
                                <span class="badge bg-danger">Rupture de stock</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center mb-4 p-3 bg-lighter rounded">
                            <div class="avatar avatar-md me-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($product->seller->sellerProfile->shop_name ?? 'Inconnu') }}&background=D4AF37&color=fff" alt="Avatar" class="rounded-circle">
                            </div>
                            <div>
                                <h6 class="mb-0">Vendu par</h6>
                                <span class="text-heading fw-bold">{{ $product->seller->sellerProfile->shop_name ?? 'Vendeur Inconnu' }}</span>
                                @if($product->seller->sellerProfile && $product->seller->sellerProfile->address)
                                    <div class="text-muted small"><i class="ti ti-map-pin me-1"></i> {{ $product->seller->sellerProfile->address }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Description</h5>
                            <p class="text-muted">
                                {{ $product->description ?? 'Aucune description fournie.' }}
                            </p>
                        </div>

                        <form action="{{ route('checkout.add', $product->id) }}" method="POST" class="d-grid gap-2">
                            @csrf
                            <input type="hidden" name="quantity" value="1"> 
                            <!-- Using POST for form submission, but could be AJAXified too -->
                            <x-feature-button 
                                feature="marketplace.add-to-cart" 
                                type="submit" 
                                class="btn btn-gold btn-lg shadow-sm">
                                <i class="ti ti-shopping-cart me-2"></i> Ajouter au panier
                            </x-feature-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Similar Products -->
    <div class="col-lg-3 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="mb-0">Similaires</h5>
            </div>
            <div class="card-body p-0">
                @php
                    // Fetch similar products (same category, excluding current)
                    $similarProducts = \App\Models\Product::where('category_id', $product->category_id)
                                        ->where('id', '!=', $product->id)
                                        ->inRandomOrder()
                                        ->take(4)
                                        ->get();
                @endphp

                @if($similarProducts->isEmpty())
                    <div class="text-center p-4 text-muted">
                        <small>Aucun produit similaire.</small>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($similarProducts as $similar)
                        <li class="list-group-item p-3">
                            <a href="{{ route('marketplace.show', $similar) }}" class="d-flex align-items-center text-body">
                                <img src="{{ $similar->thumbnail_url }}" class="rounded me-3 border" width="50" height="50" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $similar->title }}</h6>
                                    <small class="text-gold fw-bold">{{ number_format($similar->price, 0, ',', ' ') }} FCFA</small>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @endif
                
                <div class="p-3 border-top text-center">
                    <a href="{{ route('marketplace.index', ['category' => $product->category_id]) }}" class="btn btn-sm btn-label-dark w-100">
                        Voir plus
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-feature-section>
@endsection
