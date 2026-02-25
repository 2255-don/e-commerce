@extends('layouts/layoutMaster')

@section('title', $product->title)

@section('page-style')
    <style>
        /* ── Palette JOUAN-SUGU ── */
        :root {
            --js-gold: #C9A84C;
            --js-gold-light: #E2C97E;
            --js-gold-dark: #9A7A2E;
            --js-silver: #B0B8C1;
            --js-dark: #1A1D23;
            --js-surface: #22262F;
        }

        /* ── Product Card Styling ── */
        .product-details-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .06);
            border: 1px solid rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        /* Dark mode compat */
        [data-bs-theme="dark"] .product-details-card {
            background: var(--js-surface);
            border-color: rgba(255, 255, 255, .05);
        }

        /* ── Image Gallery ── */
        .product-main-image-wrapper {
            background: linear-gradient(135deg, rgba(26, 29, 35, .03) 0%, rgba(201, 168, 76, .05) 100%);
            border-radius: 12px;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(201, 168, 76, .1);
            transition: transform 0.3s ease;
        }

        [data-bs-theme="dark"] .product-main-image-wrapper {
            background: linear-gradient(135deg, rgba(255, 255, 255, .02) 0%, rgba(201, 168, 76, .05) 100%);
        }

        .product-main-image-wrapper img {
            max-height: 480px;
            object-fit: contain;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
        }

        .product-main-image-wrapper:hover img {
            transform: scale(1.06);
        }

        .product-thumbnails {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
        }

        .product-thumbnails::-webkit-scrollbar {
            height: 6px;
        }

        .product-thumbnails::-webkit-scrollbar-thumb {
            background-color: var(--js-silver);
            border-radius: 10px;
        }

        .thumb-wrapper {
            border: 2px solid transparent;
            border-radius: 10px;
            overflow: hidden;
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        [data-bs-theme="dark"] .thumb-wrapper {
            background: #2A2E39;
        }

        .thumb-wrapper:hover,
        .thumb-wrapper.active {
            border-color: var(--js-gold);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201, 168, 76, .2);
        }

        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ── Info Typography ── */
        .product-category {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--js-gold-dark);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
            display: inline-block;
            background: rgba(201, 168, 76, .1);
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
        }

        .product-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #1A1D23;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        [data-bs-theme="dark"] .product-title {
            color: #fff;
        }

        .product-price {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--js-gold), var(--js-gold-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        /* ── Seller Badge/Card ── */
        .seller-info-card {
            background: var(--js-dark);
            color: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(26, 29, 35, 0.4);
            border: 1px solid rgba(201, 168, 76, 0.3);
        }

        .seller-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--js-gold), var(--js-gold-light));
        }

        .seller-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid var(--js-gold);
            padding: 2px;
            background: var(--js-dark);
        }

        .seller-details h6 {
            color: var(--js-silver);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.2rem;
        }

        .seller-details .shop-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.2rem;
        }

        /* ── Animated Gold Button ── */
        .btn-gold {
            background: linear-gradient(135deg, var(--js-gold) 0%, var(--js-gold-light) 50%, var(--js-gold) 100%);
            background-size: 200% 100%;
            color: #1A1D23 !important;
            font-weight: 800;
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            letter-spacing: .02em;
            transition: background-position .4s ease, box-shadow .3s ease, transform .2s ease;
            box-shadow: 0 6px 20px rgba(201, 168, 76, .35);
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-gold:hover {
            background-position: 100% 0;
            box-shadow: 0 8px 28px rgba(201, 168, 76, .5);
            transform: translateY(-2px);
        }

        /* ── Stock Badges ── */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-left: 1rem;
            transform: translateY(-5px);
        }

        .stock-ok {
            background: rgba(40, 199, 111, .12);
            color: #28c76f;
            border: 1px solid rgba(40, 199, 111, .2);
        }

        .stock-out {
            background: rgba(234, 84, 85, .12);
            color: #ea5455;
            border: 1px solid rgba(234, 84, 85, .2);
        }

        .stock-unlimited {
            background: rgba(176, 184, 193, .12);
            color: #8a92a0;
            border: 1px solid rgba(176, 184, 193, .3);
        }

        /* ── Similar Products Sidebar ── */
        .similar-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            height: 100%;
            background: #fff;
        }

        [data-bs-theme="dark"] .similar-card {
            background: var(--js-surface);
            border-color: rgba(255, 255, 255, .05);
        }

        .similar-card-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        [data-bs-theme="dark"] .similar-card-header {
            border-color: rgba(255, 255, 255, .05);
        }

        .similar-card-header::before {
            content: '';
            width: 4px;
            height: 18px;
            background: var(--js-gold);
            border-radius: 2px;
        }

        .similar-item {
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            transition: background 0.2s ease;
            text-decoration: none;
            color: inherit;
        }

        [data-bs-theme="dark"] .similar-item {
            border-color: rgba(255, 255, 255, .03);
        }

        .similar-item:hover {
            background: rgba(201, 168, 76, 0.04);
        }

        .similar-item:last-child {
            border-bottom: none;
        }

        .similar-item img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid rgba(201, 168, 76, 0.2);
        }

        .similar-item-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .similar-item-price {
            font-weight: 700;
            color: var(--js-gold-dark);
            font-size: 0.85rem;
        }

        .description-content {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
            padding: 1rem 0;
        }

        [data-bs-theme="dark"] .description-content {
            color: #c8cdd8;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb & Wallet Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-2">
            <div
                style="width:4px;height:24px;border-radius:2px;background:linear-gradient(180deg,var(--js-gold),var(--js-gold-dark))">
            </div>
            <h4 class="fw-bold mb-0">
                <span style="color:var(--js-silver);font-weight:400">Boutique /</span> <span style="color:var(--js-dark)"
                    class="dark-text-white">Détails Produit</span>
            </h4>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="px-3 py-2 rounded d-flex align-items-center gap-2"
                style="background: rgba(201, 168, 76, 0.1); border: 1px solid rgba(201, 168, 76, 0.2);">
                <i class="ti ti-wallet text-gold-dark" style="color: var(--js-gold-dark);"></i>
                <span class="fw-bold" style="color: var(--js-dark);" class="dark-text-white">
                    Solde: {{ number_format(auth()->user()->wallet->balance ?? 0, 0, ',', ' ') }} FCFA
                </span>
            </div>
            <a href="{{ route('marketplace.index') }}" class="btn btn-label-secondary shadow-sm">
                <i class="ti ti-arrow-left me-1"></i> Retour à la boutique
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Product Area -->
        <div class="col-lg-8 col-xl-9">
            <div class="product-details-card p-4 p-md-5">
                <div class="row g-5">

                    <!-- Left: Images -->
                    <div class="col-md-6">
                        <div class="product-main-image-wrapper mb-4">
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}" id="mainProductImage">
                        </div>

                        @if ($product->images->count() > 1)
                            <div class="product-thumbnails">
                                @foreach ($product->images as $index => $img)
                                    <div class="thumb-wrapper {{ $index === 0 ? 'active' : '' }}"
                                        onclick="changeImage(this, '{{ asset('storage/' . $img->image_path) }}')">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Right: Details & Actions -->
                    <div class="col-md-6 d-flex flex-column">
                        <span class="product-category">{{ $product->category->name ?? 'Non classé' }}</span>

                        <h1 class="product-title">{{ $product->title }}</h1>

                        <div class="d-flex align-items-center flex-wrap">
                            <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>

                            @if ($product->type === 'service')
                                <span class="stock-badge stock-unlimited"><i class="ti ti-infinity"></i> Illimité</span>
                            @elseif ($product->stock_quantity > 0)
                                <span class="stock-badge stock-ok"><i class="ti ti-check"></i> En Stock
                                    ({{ $product->stock_quantity }})</span>
                            @else
                                <span class="stock-badge stock-out"><i class="ti ti-alert-triangle"></i> Rupture de
                                    stock</span>
                            @endif
                        </div>

                        <!-- Seller Info Box -->
                        <div class="seller-info-card">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($product->seller->shop_name ?? 'Inconnu') }}&background=22262F&color=C9A84C&size=100"
                                class="seller-avatar" alt="Avatar Boutique">
                            <div class="seller-details">
                                <h6>Vendu par</h6>
                                <div class="shop-name">{{ $product->seller->shop_name ?? 'Vendeur Inconnu' }}</div>
                                @if ($product->seller && $product->seller->address)
                                    <div style="color: var(--js-silver); font-size: 0.85rem;">
                                        <i class="ti ti-map-pin me-1"></i>{{ $product->seller->address }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description Tab-like area -->
                        <div class="mt-2 mb-4 flex-grow-1">
                            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                <i class="ti ti-file-description" style="color: var(--js-gold);"></i> Description
                            </h5>
                            <div class="description-content">
                                {{ $product->description ?? 'Aucune description détaillée n\'est disponible pour ce produit ou service.' }}
                            </div>
                        </div>

                        <!-- Add to Cart Action -->
                        <div class="mt-auto border-top pt-4" style="border-color: rgba(0,0,0,0.05) !important;">
                            @if ($product->stock_quantity > 0 || $product->type === 'service')
                                <button type="button" class="btn-gold add-to-cart-btn"
                                    data-product-id="{{ $product->id }}">
                                    <i class="ti ti-shopping-cart-plus" style="font-size: 1.3rem;"></i>
                                    <span>Ajouter au panier</span>
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary w-100 py-3 rounded-3 fw-bold disabled"
                                    style="cursor: not-allowed; opacity: 0.7;">
                                    <i class="ti ti-shopping-cart-x me-2"></i> Indisponible pour le moment
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Sidebar: Similar Products -->
        <div class="col-lg-4 col-xl-3">
            <div class="similar-card">
                <div class="similar-card-header">
                    Produits Similaires
                </div>

                @php
                    $similarProducts = \App\Models\Product::where('category_id', $product->category_id)
                        ->where('id', '!=', $product->id)
                        ->where('stock_quantity', '>', 0)
                        ->inRandomOrder()
                        ->take(5)
                        ->get();
                @endphp

                <div class="similar-list pb-2">
                    @forelse ($similarProducts as $similar)
                        <a href="{{ route('marketplace.show', $similar) }}" class="similar-item">
                            <img src="{{ $similar->thumbnail_url }}" alt="{{ $similar->title }}">
                            <div>
                                <div class="similar-item-title">{{ $similar->title }}</div>
                                <div class="similar-item-price">{{ number_format($similar->price, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-5">
                            <i class="ti ti-box-off mb-2" style="font-size: 2rem; color: var(--js-silver);"></i>
                            <div style="color: #8a92a0; font-size: 0.9rem;">Aucun produit similaire pour le moment.</div>
                        </div>
                    @endforelse
                </div>

                @if ($similarProducts->isNotEmpty())
                    <div class="p-4 text-center border-top mt-2">
                        <a href="{{ route('marketplace.index', ['category' => $product->category_id]) }}"
                            class="btn btn-sm btn-outline-secondary w-100" style="border-radius: 8px;">
                            Voir toute la catégorie
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Add extra class to help with light/dark text overrides
        document.addEventListener("DOMContentLoaded", function() {
            const theme = document.documentElement.getAttribute('data-bs-theme');
            if (theme === 'dark') {
                document.querySelectorAll('.dark-text-white').forEach(el => {
                    el.style.color = '#fff';
                });
            }

            // ---------------------------------------------------------
            // ADD TO CART (AJAX)
            // ---------------------------------------------------------
            const addBtn = document.querySelector('.add-to-cart-btn');
            const cartBadge = document.getElementById('cartBadgeCount');

            if (addBtn) {
                addBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-product-id');

                    // Save content
                    const iconEl = this.querySelector('i');
                    const textSpan = this.querySelector('span');
                    const ogIconClass = iconEl.className;
                    const ogText = textSpan.innerText;

                    // Loading State
                    iconEl.className = 'spinner-border spinner-border-sm me-2';
                    textSpan.innerText = 'Ajout en cours...';
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

                                // Show Success Toast
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
                            iconEl.className = ogIconClass;
                            textSpan.innerText = ogText;
                            this.classList.remove('disabled');
                        });
                });
            }

            // ---------------------------------------------------------
            // LOAD CART OFFCANVAS
            // ---------------------------------------------------------
            const cartOffcanvas = document.getElementById('cartOffcanvas');
            const cartOffcanvasBody = document.getElementById('cartOffcanvasBody');

            if (cartOffcanvas) {
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
                            bindRemoveButtons();
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
                                        const triggerEvent = new Event('show.bs.offcanvas');
                                        cartOffcanvas.dispatchEvent(triggerEvent);
                                    }
                                });
                        });
                    });
                }
            }
        });

        // Image gallery swapping
        function changeImage(element, imageUrl) {
            document.getElementById('mainProductImage').src = imageUrl;

            // Update active class
            document.querySelectorAll('.thumb-wrapper').forEach(el => {
                el.classList.remove('active');
            });
            element.classList.add('active');
        }
    </script>
@endsection
