@extends('layouts/layoutMaster')

@section('title', 'Mon Panier')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
@endsection

@section('page-style')
    <style>
        .premium-card {
            background: #ffffff;
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
        }

        .premium-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(212, 175, 55, 0.1);
            border-color: rgba(212, 175, 55, 0.3);
        }

        .premium-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%);
            padding: 1rem 1.5rem;
            color: #fff;
        }

        .text-gold {
            color: #D4AF37 !important;
        }

        .btn-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #E5C158 0%, #D4AF37 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            color: #fff;
        }

        .quantity-input {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .quantity-input:focus {
            border-color: #D4AF37;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1);
        }

        .summary-card {
            background: linear-gradient(180deg, #ffffff 0%, #fcfcfc 100%);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: #5d596c;
        }

        .summary-total {
            border-top: 2px dashed #e0e0e0;
            padding-top: 1.5rem;
            margin-top: 1rem;
            margin-bottom: 2rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeInDown">
            <div>
                <h4 class="fw-bold mb-1" style="color: #1a1a1a;">
                    <span class="text-muted fw-light">Boutique /</span> Mon Panier
                </h4>
                <p class="mb-0 text-muted">Gérez vos articles et procédez au paiement en toute sécurité.</p>
            </div>
            <a href="{{ route('marketplace.index') }}" class="btn btn-label-secondary">
                <i class="ti ti-arrow-left me-2"></i> Continuer mes achats
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-baseline" role="alert">
                <span class="alert-icon alert-icon-lg text-success me-2">
                    <i class="ti ti-check ti-sm"></i>
                </span>
                <div class="d-flex flex-column ps-1">
                    <h5 class="alert-heading mb-2">Succès</h5>
                    <p class="mb-0">{{ session('success') }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible d-flex align-items-baseline" role="alert">
                <span class="alert-icon alert-icon-lg text-danger me-2">
                    <i class="ti ti-alert-triangle ti-sm"></i>
                </span>
                <div class="d-flex flex-column ps-1">
                    <h5 class="alert-heading mb-2">Erreur</h5>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                @if (empty($cart))
                    <div class="premium-card text-center p-5 animate__animated animate__zoomIn">
                        <div class="mb-4">
                            <div
                                style="width: 80px; height: 80px; background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="ti ti-shopping-cart-offti-xl text-gold" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <h4 class="mb-2" style="color: #1a1a1a;">Votre panier est vide</h4>
                        <p class="text-muted mb-4">Il semblerait que vous n'ayez encore rien sélectionné.<br>Découvrez nos
                            produits d'exception.</p>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-gold px-5 py-2">
                            Commencer les achats
                        </a>
                    </div>
                @else
                    @php $delay = 0; @endphp
                    @foreach ($cart as $sellerId => $group)
                        <div class="premium-card mb-4 animate__animated animate__fadeInUp"
                            style="animation-delay: {{ $delay }}s;">
                            <div class="premium-header d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-white text-dark">
                                        <i class="ti ti-building-store"></i>
                                    </span>
                                </div>
                                <h6 class="mb-0 text-white">Vendu par : <strong>{{ $group['shop_name'] }}</strong></h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            @foreach ($group['items'] as $item)
                                                <tr>
                                                    <td style="width: 100px; padding-left: 1.5rem;">
                                                        <div class="d-flex align-items-center justify-content-center p-1 border rounded"
                                                            style="width: 80px; height: 80px;">
                                                            <img src="{{ $item['image'] }}" class="rounded"
                                                                style="width: 100%; height: 100%; object-fit: cover;">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1 text-dark fw-bold">{{ $item['title'] }}</h6>
                                                        <span class="badge bg-label-primary mb-1">En Stock</span>
                                                        <div class="text-gold fw-bold mt-1">
                                                            {{ number_format($item['price'], 0, ',', ' ') }} FCFA</div>
                                                    </td>
                                                    <td style="width: 140px;">
                                                        <form action="{{ route('checkout.update', $item['id']) }}"
                                                            method="POST"
                                                            class="d-flex align-items-center bg-lighter rounded p-1">
                                                            @csrf
                                                            <button type="button" onclick="decrement(this)"
                                                                class="btn btn-icon btn-sm btn-text-secondary rounded-pill">
                                                                <i class="ti ti-minus"></i>
                                                            </button>
                                                            <input type="number" name="quantity"
                                                                value="{{ $item['quantity'] }}" min="1"
                                                                max="{{ $item['max_stock'] }}"
                                                                class="form-control form-control-sm border-0 text-center shadow-none bg-transparent fw-bold"
                                                                style="width: 40px;" onchange="this.form.submit()">
                                                            <button type="button"
                                                                onclick="increment(this, {{ $item['max_stock'] }})"
                                                                class="btn btn-icon btn-sm btn-text-secondary rounded-pill">
                                                                <i class="ti ti-plus"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td class="text-end fw-bold" style="padding-right: 1.5rem;">
                                                        <div class="d-flex flex-column align-items-end">
                                                            <span
                                                                style="font-size: 1.1rem;">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}
                                                                FCFA</span>
                                                            <a href="{{ route('checkout.remove', $item['id']) }}"
                                                                class="text-danger small mt-2 d-flex align-items-center"
                                                                style="text-decoration: none;">
                                                                <i class="ti ti-trash me-1"></i> Retirer
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="border-top p-3 bg-lighter d-flex justify-content-end align-items-center">
                                    <span class="text-muted me-3">Sous-total vendeur :</span>
                                    <span
                                        class="fw-bold fs-5 text-dark">{{ number_format($group['subtotal'], 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            </div>
                        </div>
                        @php $delay += 0.1; @endphp
                    @endforeach
                @endif
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="premium-card summary-card p-4 animate__animated animate__fadeInRight">
                    <h5 class="card-title mb-4 fw-bold" style="color: #1a1a1a;">Résumé de la commande</h5>

                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span class="fw-semibold">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span class="text-success fw-bold">Gratuit</span>
                    </div>
                    <div class="summary-row">
                        <span>Taxe estimée</span>
                        <span>0 FCFA</span>
                    </div>

                    <div class="summary-row summary-total">
                        <span class="fw-bold fs-5 text-dark">Total à payer</span>
                        <span class="fw-bold fs-4 text-gold">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if (!empty($cart))
                        <form action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Méthode de paiement</label>
                                <div class="d-flex flex-column gap-2">
                                    <label class="card p-3 border cursor-pointer hover-bg-light transition-all mb-0">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="type" value="wallet"
                                                class="form-check-input me-3" checked>
                                            <div>
                                                <span class="d-block fw-semibold text-dark">Mon Portefeuille</span>
                                                <small class="text-muted">Solde actuel disponible</small>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="card p-3 border cursor-pointer hover-bg-light transition-all mb-0">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="type" value="cash_on_delivery"
                                                class="form-check-input me-3">
                                            <div>
                                                <span class="d-block fw-semibold text-dark">Paiement à la livraison</span>
                                                <small class="text-muted">Payer en espèces à la réception</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-gold w-100 py-3 shadow-lg">
                                <i class="ti ti-lock me-2"></i> Payer {{ number_format($total, 0, ',', ' ') }} FCFA
                            </button>
                        </form>

                        <div class="mt-4 text-center">
                            <div class="d-flex justify-content-center gap-3 text-muted">
                                <i class="ti ti-shield-check" data-bs-toggle="tooltip" title="Paiement Sécurisé"></i>
                                <i class="ti ti-truck" data-bs-toggle="tooltip" title="Livraison Rapide"></i>
                                <i class="ti ti-headset" data-bs-toggle="tooltip" title="Support 24/7"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function increment(btn, max) {
            let input = btn.previousElementSibling;
            let val = parseInt(input.value);
            if (val < max) {
                input.value = val + 1;
                input.dispatchEvent(new Event('change'));
            }
        }

        function decrement(btn) {
            let input = btn.nextElementSibling;
            let val = parseInt(input.value);
            if (val > 1) {
                input.value = val - 1;
                input.dispatchEvent(new Event('change'));
            }
        }
    </script>
@endsection
