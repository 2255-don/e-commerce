@extends('layouts/layoutMaster')

@section('title', 'Boutique - Tous les Produits')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('page-style')
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4E5B0;
            --silver-accent: #C0C0C0;
            --bg-luxury: #FDFBF7;
        }

        body {
            background-color: var(--bg-luxury);
        }

        .marketplace-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 3px solid var(--gold-primary);
            color: white;
        }

        .filter-bar {
            background: #fff;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* Softer shadow */
            margin-bottom: 2rem;
            border: 1px solid var(--silver-accent);
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: 1px solid #eaeaea;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.15) !important;
            /* Gold shadow */
            border-color: var(--gold-primary);
        }

        .product-img-container {
            position: relative;
            padding-top: 100%;
            /* 1:1 Aspect Ratio */
            overflow: hidden;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
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
            transition: transform 0.5s ease;
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
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 10;
            border: 1px solid var(--gold-light);
        }

        /* Buttons */
        .btn-gold {
            background-color: var(--gold-primary);
            color: white;
            border: none;
        }

        .btn-gold:hover {
            background-color: #B5952F;
            color: white;
            transform: translateY(-2px);
        }

        .add-to-cart-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 2px solid var(--gold-primary);
            background: white;
            color: var(--gold-primary);
        }

        .add-to-cart-btn:hover {
            background: var(--gold-primary);
            color: white;
        }

        /* Text */
        .text-gold {
            color: var(--gold-primary) !important;
        }

        /* Offcanvas Customization */
        .offcanvas-end {
            width: 400px;
            /* Wider for better view */
        }
    </style>
@endsection

@section('content')

    <!-- Header (Horizontal Layout) -->
    <div class="row mb-4 align-items-center">
        <div class="col-8">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Boutique /</span> Explorer
            </h4>
            <p class="text-muted mb-0">Découvrez nos produits d'exception</p>
        </div>
        <div class="col-4 text-end">
            <!-- Cart Button triggering Offcanvas -->
            <button type="button" class="btn btn-gold position-relative shadow-sm" data-bs-toggle="offcanvas"
                data-bs-target="#cartOffcanvas" id="headerCartBtn" style="overflow: visible;">
                <i class="ti ti-shopping-cart me-0 me-sm-1"></i>
                <span class="d-none d-sm-inline-block">Mon Panier</span>
                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm border border-white"
                    id="cartBadgeCount" style="font-size: 0.75rem; transform: translate(-50%, -50%) !important;">
                    {{ auth()->user()->cartItemsCount() ?? 0 }}
                </span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('marketplace.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-5">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text border-end-0"><i class="ti ti-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Rechercher un produit..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Category -->
                <div class="col-12 col-md-3">
                    <select name="category" class="form-select select2" data-placeholder="Catégorie">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Range -->
                <div class="col-12 col-md-2">
                    <div class="input-group input-group-sm">
                        <input type="number" name="min_price" class="form-control" placeholder="Min"
                            value="{{ request('min_price') }}">
                        <input type="number" name="max_price" class="form-control" placeholder="Max"
                            value="{{ request('max_price') }}">
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark">Filtrer</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    @if ($products->isEmpty())
        <div class="text-center py-5">
            <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}" alt="No products" width="200"
                class="mb-4">
            <h4>Aucun produit trouvé</h4>
            <p class="text-muted">Essayez de modifier vos filtres de recherche.</p>
            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-dark">Voir tout les produits</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
            @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 product-card position-relative text-center text-sm-start">
                        <!-- Seller Badge -->
                        <div class="seller-badge">
                            <i class="ti ti-building-store me-1 text-gold"></i>
                            {{ Str::limit($product->seller->shop_name ?? 'Boutique', 15) }}
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
                                <h6 class="fw-bold mb-1 text-truncate" title="{{ $product->title }}">{{ $product->title }}
                                </h6>
                            </a>

                            <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-gold fw-bold">{{ number_format($product->price, 0, ',', ' ') }} FCFA
                                </h5>

                                <!-- Add to Cart Button (AJAX) -->
                                <button type="button" class="add-to-cart-btn shadow-sm"
                                    data-product-id="{{ $product->id }}" title="Ajouter au Panier">
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
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
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
                            if (data.success) {
                                // Update Badge
                                if (cartBadge) cartBadge.innerText = data.cartCount;

                                // Show Success
                                if (typeof toastr !== 'undefined') {
                                    toastr.success('Produit ajouté au panier', 'Ajouté !', {
                                        positionClass: 'toast-top-center',
                                        timeOut: 3000,
                                        progressBar: true
                                    });
                                }
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

            cartOffcanvas.addEventListener('show.bs.offcanvas', function() {
                // Fetch Cart Details
                fetch('{{ route('checkout.details') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        cartOffcanvasBody.innerHTML = data.html;
                        bindRemoveButtons(); // Re-bind dynamic buttons
                    })
                    .catch(error => {
                        cartOffcanvasBody.innerHTML =
                            '<p class="text-danger text-center">Erreur de chargement du panier.</p>';
                    });
            });

            function bindRemoveButtons() {
                const removeBtns = cartOffcanvasBody.querySelectorAll('.remove-item-btn');
                removeBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');

                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (d.success) {
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
