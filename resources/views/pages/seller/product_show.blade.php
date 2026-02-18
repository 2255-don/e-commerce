@extends('layouts/layoutMaster')

@section('title', 'Détails du Produit')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endsection

@section('page-style')
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4E5B0;
            --gold-dark: #B5952F;
            --navy-dark: #1a1a1a;
            --navy-light: #2c2c2c;
        }

        body {
            background-color: #f8f9fa;
        }

        /* Card Styling */
        .premium-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            /* Soft, luxurious shadow */
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.15);
            /* Gold glow on hover */
        }

        /* Images */
        .main-image-container {
            border-radius: 12px;
            overflow: hidden;
            background: radial-gradient(circle at center, #fdfbf7 0%, #f3f3f3 100%);
            padding: 2rem;
            transition: all 0.5s ease;
        }

        .main-image {
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            max-height: 400px;
            object-fit: contain;
            width: 100%;
        }

        .main-image:hover {
            transform: scale(1.08);
            /* Subtle zoom */
        }

        .thumb-img {
            cursor: pointer;
            opacity: 0.6;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .thumb-img:hover,
        .thumb-img.active {
            opacity: 1;
            border-color: var(--gold-primary);
            transform: scale(1.05);
        }

        /* Typography */
        .text-gold {
            color: var(--gold-primary) !important;
        }

        .bg-gold {
            background-color: var(--gold-primary) !important;
            color: white;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: var(--navy-dark);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--gold-primary);
            border-radius: 2px;
        }

        /* Buttons */
        .btn-gold-outline {
            color: var(--gold-dark);
            border: 2px solid var(--gold-primary);
            background: transparent;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gold-outline:hover {
            background: var(--gold-primary);
            color: white;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        .action-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: rotate(15deg) scale(1.1);
        }

        /* Stat Cards */
        .stat-box {
            background: #fdfbf7;
            border-left: 4px solid var(--gold-primary);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .badge-premium {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-dark) 100%);
            color: white;
            border: none;
            padding: 0.5em 1em;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="row animate__animated animate__fadeIn">
        <!-- Breadcrumb & Header -->
        <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1 section-title" style="margin-left: 0;">{{ $product->title }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}"
                                class="text-muted">Boutique</a></li>
                        <li class="breadcrumb-item active text-gold" aria-current="page">Détails</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('seller.products.edit', $product->id) }}" class="btn btn-gold-outline me-2">
                    <i class="ti ti-edit me-1"></i> Modifier
                </a>
                <button type="button" class="btn btn-label-danger" onclick="confirmDelete('{{ $product->id }}')">
                    <i class="ti ti-trash me-1"></i> Supprimer
                </button>
                <form id="delete-form-{{ $product->id }}" action="{{ route('seller.products.destroy', $product->id) }}"
                    method="POST" style="display: none;">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="col-lg-8 mb-4">
            <!-- Image Gallery Card -->
            <div class="premium-card p-4 h-100">
                <div class="main-image-container mb-4 text-center">
                    <img src="{{ $product->thumbnail_url }}" id="mainImage" class="main-image img-fluid"
                        alt="{{ $product->title }}">
                </div>

                @if ($product->images->count() > 1)
                    <div class="d-flex gap-3 justify-content-center overflow-auto py-2">
                        <img src="{{ $product->thumbnail_url }}" class="rounded thumb-img active" width="80"
                            height="80" style="object-fit: cover;"
                            onclick="swapImage(this, '{{ $product->thumbnail_url }}')">
                        @foreach ($product->images as $img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="rounded thumb-img" width="80"
                                height="80" style="object-fit: cover;"
                                onclick="swapImage(this, '{{ asset('storage/' . $img->image_path) }}')">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / Info Details -->
        <div class="col-lg-4 mb-4">
            <div class="premium-card p-4 h-100 animate__animated animate__fadeInRight" style="animation-delay: 0.1s;">

                <!-- Price & Status -->
                <div class="mb-4 text-center">
                    <h2 class="text-gold fw-bolder display-6 mb-2">{{ number_format($product->price, 0, ',', ' ') }} FCFA
                    </h2>
                    <div class="d-flex justify-content-center gap-2">
                        <span
                            class="badge {{ $product->stock_quantity > 0 ? 'bg-label-success' : 'bg-label-danger' }} px-3">
                            {{ $product->stock_quantity > 0 ? 'En Stock' : 'Épuisé' }}
                        </span>
                        <span class="badge bg-label-info">{{ $product->category->name ?? 'Non classé' }}</span>
                    </div>
                </div>

                <hr class="border-light my-4">

                <!-- Key Info -->
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="ti ti-box me-2"></i>Quantité</span>
                        <span class="fw-bold fs-5">{{ $product->stock_quantity }}</span>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="ti ti-tag me-2"></i>Type</span>
                        <span class="fw-bold">{{ $product->type === 'service' ? 'Service' : 'Bien Physique' }}</span>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="ti ti-calendar me-2"></i>Ajouté le</span>
                        <span class="fw-bold">{{ $product->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <!-- Commission Info -->
                <div class="alert alert-label-warning d-flex align-items-center mb-0" role="alert">
                    <span class="alert-icon text-warning me-2">
                        <i class="ti ti-info-circle ti-xs"></i>
                    </span>
                    <div class="d-flex flex-column ps-1">
                        <h6 class="alert-heading mb-1 small text-uppercase fw-bold text-warning">Commission
                            ({{ number_format($product->seller->commission_rate ?? 1, 0) }}%)</h6>
                        <span class="small mb-1">Prélevé sur chaque vente.</span>
                        <span class="fw-bold text-dark">
                            -
                            {{ number_format($product->price * (($product->seller->commission_rate ?? 1) / 100), 0, ',', ' ') }}
                            FCFA
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <h6 class="fw-bold"><i class="ti ti-file-description me-1 text-gold"></i> Description</h6>
                    <p class="text-muted small" style="line-height: 1.6;">
                        {{ $product->description ?? 'Aucune description disponible pour ce produit.' }}
                    </p>
                </div>

                <!-- Seller Info Quick View -->
                <div class="mt-auto pt-4 border-top">
                    <div class="d-flex align-items-center bg-lighter p-3 rounded">
                        <div class="avatar avatar-sm me-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($product->seller->shop_name ?? 'Vous') }}&background=D4AF37&color=fff"
                                class="rounded-circle">
                        </div>
                        <div>
                            <small class="text-muted d-block">Vendeur</small>
                            <span class="fw-bold text-dark">{{ $product->seller->shop_name ?? 'Votre Boutique' }}</span>
                        </div>
                    </div>
                    <a href="{{ route('marketplace.show', $product->id) }}" target="_blank"
                        class="btn btn-label-dark w-100 mt-3">
                        <i class="ti ti-external-link me-1"></i> Voir sur le Marketplace
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
    <script>
        function swapImage(el, src) {
            // Change main image
            const mainImg = document.getElementById('mainImage');
            mainImg.style.opacity = '0';

            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = '1';
            }, 200);

            // Update active class
            document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active', 'border-primary'));
            el.classList.add('active');
        }

        function confirmDelete(productId) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + productId).submit();
                }
            })
        }
    </script>
@endsection
