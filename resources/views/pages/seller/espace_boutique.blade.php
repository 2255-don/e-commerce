@extends('layouts/layoutMaster')

@section('title', 'Espace Boutique')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

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

        /* ── Hero Banner ── */
        .seller-hero {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: linear-gradient(135deg, #1A1D23 0%, #2C3040 50%, #1A1D23 100%);
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .25);
        }

        .seller-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(201, 168, 76, .18) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(176, 184, 193, .10) 0%, transparent 60%);
            pointer-events: none;
        }

        /* Subtle animated shimmer */
        .seller-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 40%;
            height: 200%;
            background: linear-gradient(105deg, transparent 40%, rgba(255, 255, 255, .06) 50%, transparent 60%);
            animation: shimmer 6s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% {
                left: -60%;
            }

            100% {
                left: 160%;
            }
        }

        .seller-hero-inner {
            position: relative;
            z-index: 1;
            padding: 2rem 2rem 1.5rem;
            display: flex;
            align-items: flex-end;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        /* Shop logo */
        .shop-avatar {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            object-fit: cover;
            border: 3px solid var(--js-gold);
            box-shadow: 0 0 0 4px rgba(201, 168, 76, .25), 0 8px 24px rgba(0, 0, 0, .4);
            flex-shrink: 0;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .shop-avatar:hover {
            transform: scale(1.04);
            box-shadow: 0 0 0 6px rgba(201, 168, 76, .35), 0 12px 32px rgba(0, 0, 0, .5);
        }

        .seller-hero-info h3 {
            color: #fff;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: .25rem;
            letter-spacing: .02em;
        }

        .seller-hero-info .licence-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(201, 168, 76, .15);
            border: 1px solid rgba(201, 168, 76, .35);
            color: var(--js-gold-light);
            font-size: .8rem;
            padding: .3rem .75rem;
            border-radius: 50px;
            backdrop-filter: blur(4px);
        }

        .seller-hero-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--js-gold) 0%, var(--js-gold-light) 50%, var(--js-gold) 100%);
            background-size: 200% 100%;
            color: #1A1D23 !important;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: .6rem 1.4rem;
            letter-spacing: .03em;
            transition: background-position .4s ease, box-shadow .3s ease, transform .2s ease;
            box-shadow: 0 4px 16px rgba(201, 168, 76, .35);
        }

        .btn-gold:hover {
            background-position: 100% 0;
            box-shadow: 0 6px 24px rgba(201, 168, 76, .5);
            transform: translateY(-2px);
        }

        /* ── Stat Cards ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            border: 1px solid rgba(0, 0, 0, .05);
            transition: transform .25s ease, box-shadow .25s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--js-gold), var(--js-gold-dark));
            border-radius: 4px 0 0 4px;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-icon.gold {
            background: rgba(201, 168, 76, .12);
            color: var(--js-gold-dark);
        }

        .stat-icon.silver {
            background: rgba(176, 184, 193, .18);
            color: #5a6475;
        }

        .stat-icon.green {
            background: rgba(40, 199, 111, .12);
            color: #28c76f;
        }

        .stat-icon.red {
            background: rgba(234, 84, 85, .12);
            color: #ea5455;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2c3040;
            line-height: 1;
            margin-bottom: .2rem;
        }

        .stat-label {
            font-size: .75rem;
            color: #8a92a0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ── Product Table Card ── */
        .products-card {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, .06);
            box-shadow: 0 2px 16px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .products-card .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .07);
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
        }

        .products-card .card-header h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3040;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .products-card .card-header h5 .header-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--js-gold), var(--js-gold-light));
            display: inline-block;
        }

        /* Search input */
        .search-wrapper {
            position: relative;
        }

        .search-wrapper input {
            padding-left: 2.2rem;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, .1);
            font-size: .85rem;
            height: 36px;
            width: 220px;
            transition: border-color .2s, box-shadow .2s;
        }

        .search-wrapper input:focus {
            border-color: var(--js-gold);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, .15);
            outline: none;
        }

        .search-wrapper .search-icon {
            position: absolute;
            left: .65rem;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: .9rem;
            pointer-events: none;
        }

        /* Table */
        .products-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .products-table thead th {
            background: #f8f9fb;
            color: #8a92a0;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: .85rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, .07);
            white-space: nowrap;
        }

        .products-table tbody tr {
            transition: background .18s ease;
        }

        .products-table tbody tr:hover {
            background: rgba(201, 168, 76, .04);
        }

        .products-table tbody td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            vertical-align: middle;
            font-size: .875rem;
            color: #3d4455;
        }

        .products-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Product thumbnail */
        .product-thumb {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid rgba(201, 168, 76, .2);
            flex-shrink: 0;
        }

        .product-title {
            font-weight: 600;
            color: #2c3040;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-type-badge {
            display: inline-block;
            font-size: .7rem;
            padding: .15rem .5rem;
            border-radius: 4px;
            font-weight: 600;
            background: rgba(201, 168, 76, .1);
            color: var(--js-gold-dark);
            margin-top: .2rem;
        }

        /* Price */
        .price-text {
            font-weight: 700;
            color: var(--js-gold-dark);
            white-space: nowrap;
        }

        /* Stock badge */
        .stock-ok {
            background: rgba(40, 199, 111, .12);
            color: #1a9e55;
        }

        .stock-low {
            background: rgba(255, 159, 67, .12);
            color: #c97a10;
        }

        .stock-out {
            background: rgba(234, 84, 85, .12);
            color: #c0392b;
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .25rem .65rem;
            border-radius: 6px;
            font-size: .78rem;
            font-weight: 600;
        }

        /* Status badge */
        .status-active {
            background: rgba(40, 199, 111, .12);
            color: #1a9e55;
            padding: .25rem .75rem;
            border-radius: 6px;
            font-size: .78rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .status-active::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #28c76f;
            display: inline-block;
        }

        /* Action buttons */
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            transition: all .2s ease;
            cursor: pointer;
            background: transparent;
            font-size: .95rem;
            text-decoration: none;
        }

        .action-btn.view {
            color: #6c757d;
            border-color: rgba(108, 117, 125, .2);
        }

        .action-btn.edit {
            color: var(--js-gold-dark);
            border-color: rgba(201, 168, 76, .25);
        }

        .action-btn.del {
            color: #ea5455;
            border-color: rgba(234, 84, 85, .2);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
        }

        .action-btn.view:hover {
            background: rgba(108, 117, 125, .08);
        }

        .action-btn.edit:hover {
            background: rgba(201, 168, 76, .1);
        }

        .action-btn.del:hover {
            background: rgba(234, 84, 85, .08);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(201, 168, 76, .12), rgba(201, 168, 76, .06));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2rem;
            color: var(--js-gold);
        }

        .empty-state h6 {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3040;
            margin-bottom: .5rem;
        }

        .empty-state p {
            color: #8a92a0;
            font-size: .875rem;
            margin-bottom: 1.25rem;
        }

        /* Alert */
        .alert-success-custom {
            background: rgba(40, 199, 111, .08);
            border: 1px solid rgba(40, 199, 111, .25);
            border-left: 4px solid #28c76f;
            border-radius: 10px;
            color: #1a9e55;
            padding: .85rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.25rem;
            animation: slideDown .3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Fade-in rows */
        .products-table tbody tr {
            animation: fadeInRow .3s ease both;
        }

        @keyframes fadeInRow {
            from {
                opacity: 0;
                transform: translateX(-6px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .products-table tbody tr:nth-child(1) {
            animation-delay: .04s;
        }

        .products-table tbody tr:nth-child(2) {
            animation-delay: .08s;
        }

        .products-table tbody tr:nth-child(3) {
            animation-delay: .12s;
        }

        .products-table tbody tr:nth-child(4) {
            animation-delay: .16s;
        }

        .products-table tbody tr:nth-child(5) {
            animation-delay: .20s;
        }

        .products-table tbody tr:nth-child(n+6) {
            animation-delay: .24s;
        }

        /* Dark mode compat */
        [data-bs-theme="dark"] .stat-card,
        [data-bs-theme="dark"] .products-card .card-header,
        [data-bs-theme="dark"] .products-card {
            background: #2c3040;
            border-color: rgba(255, 255, 255, .07);
        }

        [data-bs-theme="dark"] .products-table thead th {
            background: #252836;
        }

        [data-bs-theme="dark"] .products-table tbody td {
            color: #c8cdd8;
            border-color: rgba(255, 255, 255, .05);
        }

        [data-bs-theme="dark"] .stat-value {
            color: #e4e8f0;
        }

        [data-bs-theme="dark"] .search-wrapper input {
            background: #252836;
            color: #c8cdd8;
            border-color: rgba(255, 255, 255, .1);
        }
    </style>
@endsection

@section('content')

    {{-- Page Title --}}
    <div class="d-flex align-items-center mb-4 gap-2">
        <div
            style="width:4px;height:24px;border-radius:2px;background:linear-gradient(180deg,var(--js-gold),var(--js-gold-dark))">
        </div>
        <h4 class="fw-bold mb-0" style="color:#2c3040">
            <span style="color:#aaa;font-weight:400">Vendeur /</span> Espace Boutique
        </h4>
    </div>

    {{-- Flash Message --}}
    @if (session('status'))
        <div class="alert-success-custom">
            <i class="ti ti-circle-check" style="font-size:1.2rem"></i>
            <span>
                @if (session('status') === 'product-created')
                    Produit ajouté avec succès !
                @elseif(session('status') === 'product-updated')
                    Produit mis à jour avec succès !
                @else
                    Produit supprimé.
                @endif
            </span>
            <button type="button" onclick="this.closest('.alert-success-custom').remove()"
                style="margin-left:auto;background:none;border:none;cursor:pointer;color:inherit;font-size:1.1rem;line-height:1">×</button>
        </div>
    @endif

    {{-- ── Hero Banner ── --}}
    <div class="seller-hero">
        <div class="seller-hero-inner">
            <img src="{{ $shopLogoUrl }}" alt="Logo boutique" class="shop-avatar">

            <div class="seller-hero-info">
                <h3>{{ $seller->shop_name }}</h3>
                <span class="licence-badge">
                    <i class="ti ti-shield-check"></i>
                    Licence active · expire le {{ $seller->licence_expire_at->format('d/m/Y') }}
                </span>
            </div>

            <div class="seller-hero-actions">
                <x-feature-link feature="seller.products.create" route="{{ route('seller.products.create') }}"
                    class="btn btn-gold">
                    <i class="ti ti-plus me-1"></i> Ajouter un Produit
                </x-feature-link>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    @php
        $totalProducts = $products->count();
        $activeProducts = $products->where('status', 'active')->count() ?: $totalProducts;
        $outOfStock = $products->where('type', 'physical_good')->where('stock_quantity', '<=', 0)->count();
        $services = $products->where('type', 'service')->count();
    @endphp
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon gold"><i class="ti ti-package"></i></div>
            <div>
                <div class="stat-value">{{ $totalProducts }}</div>
                <div class="stat-label">Total Produits</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="ti ti-circle-check"></i></div>
            <div>
                <div class="stat-value">{{ $activeProducts }}</div>
                <div class="stat-label">Actifs</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon silver"><i class="ti ti-tools"></i></div>
            <div>
                <div class="stat-value">{{ $services }}</div>
                <div class="stat-label">Services</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="ti ti-alert-triangle"></i></div>
            <div>
                <div class="stat-value">{{ $outOfStock }}</div>
                <div class="stat-label">Rupture de stock</div>
            </div>
        </div>
    </div>

    {{-- ── Products Table ── --}}
    <div class="card products-card">
        <div class="card-header">
            <h5>
                <span class="header-dot"></span>
                Mes Produits
            </h5>
            <div class="search-wrapper">
                <i class="ti ti-search search-icon"></i>
                <input type="text" id="productSearch" placeholder="Rechercher un produit…"
                    oninput="filterProducts(this.value)">
            </div>
        </div>

        <x-feature-section feature="seller.products.view-list" showDeniedMessage="true">
            <div class="table-responsive">
                <table class="products-table" id="productsTable">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr data-name="{{ strtolower($product->title) }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->title }}"
                                            class="product-thumb">
                                        <div>
                                            <div class="product-title">{{ $product->title }}</div>
                                            <span class="product-type-badge">
                                                {{ $product->type === 'service' ? 'Service' : 'Bien physique' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name ?? 'Non classé' }}</td>
                                <td>
                                    <span class="price-text">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                </td>
                                <td>
                                    @if ($product->type === 'service')
                                        <span class="stock-badge" style="background:rgba(176,184,193,.15);color:#5a6475">
                                            <i class="ti ti-infinity" style="font-size:.8rem"></i> Illimité
                                        </span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="stock-badge stock-out">
                                            <i class="ti ti-alert-circle" style="font-size:.8rem"></i> Rupture
                                        </span>
                                    @elseif($product->stock_quantity <= 5)
                                        <span class="stock-badge stock-low">
                                            <i class="ti ti-alert-triangle" style="font-size:.8rem"></i>
                                            {{ $product->stock_quantity }}
                                        </span>
                                    @else
                                        <span class="stock-badge stock-ok">
                                            <i class="ti ti-check" style="font-size:.8rem"></i>
                                            {{ $product->stock_quantity }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-active">Actif</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('seller.products.show', $product->id) }}" class="action-btn view"
                                            title="Voir le produit">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <x-feature-link feature="seller.products.edit"
                                            route="{{ route('seller.products.edit', $product->id) }}"
                                            class="action-btn edit" title="Modifier">
                                            <i class="ti ti-edit"></i>
                                        </x-feature-link>
                                        <form id="delete-form-{{ $product->id }}"
                                            action="{{ route('seller.products.destroy', $product->id) }}" method="POST"
                                            style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-feature-button feature="seller.products.destroy" type="button"
                                                onclick="confirmDelete('{{ $product->id }}')" class="action-btn del"
                                                title="Supprimer">
                                                <i class="ti ti-trash"></i>
                                            </x-feature-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="ti ti-shopping-bag"></i>
                                        </div>
                                        <h6>Votre boutique est vide</h6>
                                        <p>Commencez à vendre en ajoutant votre premier produit.</p>
                                        <a href="{{ route('seller.products.create') }}" class="btn btn-gold">
                                            <i class="ti ti-plus me-1"></i> Ajouter mon premier produit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-feature-section>
    </div>

@endsection

@section('page-script')
    <script>
        /* ── Delete confirmation ── */
        function confirmDelete(productId) {
            Swal.fire({
                title: 'Supprimer ce produit ?',
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary',
                    popup: 'rounded-4'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + productId).submit();
                }
            });
        }

        /* ── Live search ── */
        function filterProducts(query) {
            const rows = document.querySelectorAll('#productsTable tbody tr[data-name]');
            const q = query.toLowerCase().trim();
            rows.forEach(row => {
                row.style.display = (!q || row.dataset.name.includes(q)) ? '' : 'none';
            });
        }
    </script>
@endsection
