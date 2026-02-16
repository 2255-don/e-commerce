@extends('layouts.layoutMaster')

@section('title', 'Boutique - Tous les Produits')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        /* === ANIMATIONS === */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-product-card {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }

        /* === PRODUCT CARD === */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(213, 175, 96, 0.2), 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #d5af60;
        }

        /* === PRODUCT IMAGE === */
        .product-img-container {
            position: relative;
            padding-top: 100%;
            overflow: hidden;
            background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
            border-bottom: 1px solid #f3f4f6;
        }

        .product-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 20px;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card:hover .product-img {
            transform: scale(1.08);
        }

        /* === SELLER BADGE === */
        .seller-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #111827;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            z-index: 10;
            border: 1px solid rgba(213, 175, 96, 0.3);
            letter-spacing: 0.3px;
        }

        /* === PRODUCT TITLE === */
        .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.4;
            transition: color 0.2s;
        }

        .product-card:hover .product-title {
            color: #d5af60;
        }

        /* === PRODUCT PRICE === */
        .product-price {
            font-size: 1.35rem;
            font-weight: 700;
            color: #d5af60;
            letter-spacing: -0.5px;
        }

        /* === ADD TO CART BUTTON === */
        .add-to-cart-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #d5af60;
            background: white;
            color: #d5af60;
            box-shadow: 0 2px 4px rgba(213, 175, 96, 0.2);
        }

        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, #d5af60 0%, #c49a48 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(213, 175, 96, 0.4);
        }

        .add-to-cart-btn:active {
            transform: scale(0.95);
        }

        /* === CART BADGE FIX === */
        .cart-btn-wrapper {
            position: relative;
            display: inline-block;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            border-radius: 11px;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        /* === FILTER BAR === */
        .filter-bar-brand {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .filter-input-compact {
            height: 42px;
        }

        .filter-label-compact {
            font-size: 0.8rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
        }

        /* === CART OFFCANVAS === */
        .offcanvas-end {
            width: 420px;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
        }

        /* === CATEGORY BADGE === */
        .category-badge {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 12px;
            letter-spacing: 0.3px;
        }

        /* === RESPONSIVE GRID GAPS === */
        @media (min-width: 768px) {
            .product-grid {
                gap: 1.5rem !important;
            }
        }

        @media (min-width: 1024px) {
            .product-grid {
                gap: 2rem !important;
            }
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
            <div class="cart-btn-wrapper">
                <!-- Cart Button -->
                <x-feature-button feature="marketplace.view-cart" type="button" class="btn btn-brand-primary"
                    data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                    <i class='bx bxs-cart'></i>
                    <span class="d-none d-sm-inline-block ms-1">Mon Panier</span>
                </x-feature-button>
                <span class="cart-badge badge bg-danger" id="cartBadgeCount">
                    {{ auth()->check() ? auth()->user()->cartItemsCount() : 0 }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar-brand mb-5">
        <div>
            <form action="{{ route('marketplace.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <!-- Search -->
                    <div class="col-12 col-md-4">
                        <label class="filter-label-compact">
                            <i class='bx bx-search'></i> Rechercher
                        </label>
                        <input type="text" name="search" class="form-control filter-input-compact"
                            placeholder="Rechercher un produit..." value="{{ request('search') }}">
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-3">
                        <label class="filter-label-compact">
                            <i class='bx bx-category'></i> Catégorie
                        </label>
                        <select name="category" class="form-control filter-input-compact">
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
                    <div class="col-12 col-md-3">
                        <label class="filter-label-compact">
                            <i class='bx bx-money'></i> Prix (FCFA)
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control filter-input-compact"
                                    placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control filter-input-compact"
                                    placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-brand-primary w-100 filter-input-compact">
                            <i class='bx bx-filter'></i>
                            Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
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
        {{-- Product Grid: Visible to ALL users --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 product-grid g-3 mb-5">
            @foreach ($products as $index => $product)
                <div class="col animate-product-card" style="animation-delay: {{ $index * 0.05 }}s;">
                    <div class="card h-100 product-card position-relative text-center text-sm-start">
                        <!-- Seller Badge -->
                        <div class="seller-badge">
                            <i class="ti ti-building-store me-1 text-gold"></i>
                            {{ Str::limit($product->seller->sellerProfile->shop_name ?? 'Boutique', 15) }}
                        </div>

                        <!-- Image -->
                        <div class="product-img-container">
                            <a href="{{ route('marketplace.show', $product) }}">
                                <img src="{{ $product->thumbnail_url }}" class="product-img" alt="{{ $product->title }}">
                            </a>
                        </div>

                        <!-- Body -->
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3">
                                <span class="category-badge">{{ $product->category->name }}</span>
                            </div>

                            <a href="{{ route('marketplace.show', $product) }}" class="text-decoration-none">
                                <h6 class="product-title mb-2" title="{{ $product->title }}">{{ $product->title }}</h6>
                            </a>

                            <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                                <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>

                                {{-- Add to Cart Button: Protected by feature-button (requires login) --}}
                                <x-feature-button feature="marketplace.add-to-cart" type="button"
                                    class="add-to-cart-btn shadow-sm" data-product-id="{{ $product->id }}"
                                    title="Ajouter au Panier">
                                    <i class="ti ti-plus fs-4"></i>
                                </x-feature-button>
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

            cartOffcanvas.addEventListener('show.bs.offcanvas', function() {
                // Fetch Cart Details
                fetch('{{ route('cart.sidebar') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
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
