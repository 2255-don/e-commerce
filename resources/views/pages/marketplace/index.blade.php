@extends('layouts/layoutMaster')

@section('title', 'Boutique - Tous les Produits')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<style>
    .product-card {
        transition: all var(--transition-smooth);
        height: 100%;
        border: 1px solid var(--grey-200);
        background: white;
        border-radius: var(--radius-lg);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-gold-lg);
        border-color: var(--brand-gold-light);
    }
    
    .product-img-container {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
        border-top-left-radius: var(--radius-lg);
        border-top-right-radius: var(--radius-lg);
        background: #fff;
    }
    
    .product-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        transition: transform var(--transition-slow);
    }
    
    .product-card:hover .product-img {
        transform: scale(1.05);
    }
    
    .seller-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.95);
        padding: 5px 12px;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--grey-900);
        box-shadow: var(--shadow-sm);
        z-index: 10;
        border: 1px solid var(--brand-gold-light);
    }

    .add-to-cart-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-base);
        border: 2px solid var(--brand-gold);
        background: white;
        color: var(--brand-gold);
    }
    
    .add-to-cart-btn:hover {
        background: var(--brand-gold);
        color: white;
    }

    .offcanvas-end {
        width: 400px;
    }
</style>
@endsection

@section('content')

<!-- Header -->
<div class="page-header-brand animate-fade-in-up mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title-brand">
                <i class='bx bxs-store text-brand-gold'></i>
                Marketplace
            </h2>
            <p class="page-subtitle-brand">Découvrez nos produits d'exception</p>
        </div>
        <div>
            <!-- Cart Button -->
            <button type="button" class="btn btn-brand-primary position-relative" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" id="headerCartBtn">
                <i class='bx bxs-cart'></i>
                <span class="d-none d-sm-inline-block ms-1">Mon Panier</span>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadgeCount" style="transform: translate(-50%, -50%) !important;">
                    {{ auth()->check() ? auth()->user()->cartItemsCount() : 0 }} 
                </span>
            </button>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card-brand mb-4 animate-fade-in-up" style="animation-delay: 0.1s;">
    <div class="card-body">
        <form action="{{ route('marketplace.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <!-- Search -->
                <div class="col-12 col-md-4">
                    <label class="form-label-brand">
                        <i class='bx bx-search'></i> Rechercher
                    </label>
                    <input type="text" name="search" class="form-control-brand" placeholder="Rechercher un produit..." value="{{ request('search') }}">
                </div>
                
                <!-- Category -->
                <div class="col-12 col-md-3">
                    <label class="form-label-brand">
                        <i class='bx bx-category'></i> Catégorie
                    </label>
                    <select name="category" class="form-control-brand">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div class="col-12 col-md-3">
                    <label class="form-label-brand">
                        <i class='bx bx-money'></i> Prix (FCFA)
                    </label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control-brand" placeholder="Min" value="{{ request('min_price') }}">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control-brand" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-brand-primary w-100">
                        <i class='bx bx-filter'></i>
                        Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Product Grid -->
@if($products->isEmpty())
    <div class="text-center py-5">
        <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}" alt="No products" width="200" class="mb-4">
        <h4>Aucun produit trouvé</h4>
        <p class="text-muted">Essayez de modifier vos filtres de recherche.</p>
        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-dark">Voir tout les produits</a>
    </div>
@else
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        @foreach($products as $product)
            <div class="col">
                <div class="card h-100 product-card position-relative text-center text-sm-start">
                    <!-- Seller Badge -->
                    <div class="seller-badge">
                        <i class="ti ti-building-store me-1 text-gold"></i> {{ Str::limit($product->seller->sellerProfile->shop_name ?? 'Boutique', 15) }}
                    </div>

                    <!-- Image -->
                    <div class="product-img-container">
                        <a href="{{ route('marketplace.show', $product) }}">
                            <img src="{{ $product->thumbnail_url }}" class="product-img" alt="{{ $product->title }}">
                        </a>
                    </div>
                    
                    <!-- Body -->
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-label-secondary">{{ $product->category->name }}</span>
                        </div>
                        
                        <a href="{{ route('marketplace.show', $product) }}" class="text-body">
                            <h6 class="fw-bold mb-1 text-truncate" title="{{ $product->title }}">{{ $product->title }}</h6>
                        </a>
                        
                        <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-gold fw-bold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</h5>
                            
                            <!-- Add to Cart Button (AJAX) -->
                            <button type="button" 
                                    class="add-to-cart-btn shadow-sm"
                                    data-product-id="{{ $product->id }}"
                                    title="Ajouter au Panier">
                                <i class="ti ti-plus fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
@endif

<!-- Cart Offcanvas (Side Modal) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="cartOffcanvasLabel" class="offcanvas-title fw-bold">Mon Panier</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body position-relative" id="cartOffcanvasBody">
        <div class="text-center py-5">
            <div class="spinner-border text-gold" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Select2
        $(".select2").select2();

        // ---------------------------------------------------------
        // ADD TO CART (AJAX)
        // ---------------------------------------------------------
        const addBtns = document.querySelectorAll('.add-to-cart-btn');
        const cartBadge = document.getElementById('cartBadgeCount');

        addBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const originalContent = this.innerHTML;

                // Loading State
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                this.classList.add('disabled');

                // Fetch
                fetch(`/cart/add/${productId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Update Badge
                        if(cartBadge) cartBadge.innerText = data.cartCount;
                        
                        // Show Success (Use SweetAlert if available or default alert)
                         Swal.fire({
                            icon: 'success',
                            title: 'Ajouté !',
                            text: 'Produit ajouté au panier',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                         });
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    // Reset Button
                    this.innerHTML = originalContent;
                    this.classList.remove('disabled');
                });
            });
        });

        // ---------------------------------------------------------
        // LOAD CART OFFCANVAS
        // ---------------------------------------------------------
        const cartOffcanvas = document.getElementById('cartOffcanvas');
        const cartOffcanvasBody = document.getElementById('cartOffcanvasBody');

        cartOffcanvas.addEventListener('show.bs.offcanvas', function () {
            // Fetch Cart Details
            fetch('{{ route("checkout.details") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                cartOffcanvasBody.innerHTML = data.html;
                bindRemoveButtons(); // Re-bind dynamic buttons
            })
            .catch(error => {
                cartOffcanvasBody.innerHTML = '<p class="text-danger text-center">Erreur de chargement du panier.</p>';
            });
        });

        function bindRemoveButtons() {
            const removeBtns = cartOffcanvasBody.querySelectorAll('.remove-item-btn');
            removeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(d => {
                        if(d.success) {
                             // Reload Cart Content
                             // We trigger the event listener logic manually or just re-fetch
                             const triggerEvent = new Event('show.bs.offcanvas');
                             cartOffcanvas.dispatchEvent(triggerEvent);
                             
                             // Also update badge
                             // Ideally we should return count in remove response too, let's assume valid state on reload
                        }
                    });
                });
            });
        }
    });
</script>
@endsection
