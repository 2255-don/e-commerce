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
            -moz-appearance: textfield;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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
                                                        <div class="d-flex align-items-center bg-lighter rounded p-1">
                                                            <button type="button"
                                                                onclick="updateQuantityAjax('{{ $item['id'] }}', -1, {{ $item['max_stock'] }})"
                                                                class="btn btn-icon btn-sm btn-text-secondary rounded-pill">
                                                                <i class="ti ti-minus"></i>
                                                            </button>
                                                            <input type="number" id="qty-{{ $item['id'] }}"
                                                                value="{{ $item['quantity'] }}" min="1"
                                                                max="{{ $item['max_stock'] }}"
                                                                class="form-control form-control-sm border-0 text-center shadow-none bg-transparent fw-bold quantity-input px-1"
                                                                style="width: 50px;"
                                                                onchange="updateQuantityAjax('{{ $item['id'] }}', this.value - this.defaultValue, {{ $item['max_stock'] }}, this.value)"
                                                                data-default="{{ $item['quantity'] }}">
                                                            <button type="button"
                                                                onclick="updateQuantityAjax('{{ $item['id'] }}', 1, {{ $item['max_stock'] }})"
                                                                class="btn btn-icon btn-sm btn-text-secondary rounded-pill">
                                                                <i class="ti ti-plus"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold" style="padding-right: 1.5rem;">
                                                        <div class="d-flex flex-column align-items-end">
                                                            <span id="item-total-{{ $item['id'] }}"
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
                                    <span id="seller-subtotal-{{ $sellerId }}"
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
                        <span class="fw-semibold cart-subtotal">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
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
                        <span class="fw-bold fs-4 text-gold cart-total">{{ number_format($total, 0, ',', ' ') }}
                            FCFA</span>
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
                                <i class="ti ti-lock me-2"></i> Payer <span
                                    class="cart-btn-total">{{ number_format($total, 0, ',', ' ') }}</span> FCFA
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
        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount).replace(/\s/g, ' ');
        }

        async function updateQuantityAjax(productId, change, max, exactValue = null) {
            let input = document.getElementById(`qty-${productId}`);
            let currentQty = parseInt(input.value);
            let newQty = exactValue !== null ? parseInt(exactValue) : currentQty + change;

            if (newQty < 1 || newQty > max) return;

            // Set optimistically
            input.value = newQty;
            input.setAttribute('data-default', newQty);

            try {
                let response = await fetch(`/cart/update/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: newQty
                    })
                });

                let data = await response.json();

                if (data.success) {
                    // Update Item Total
                    let itemTotalEl = document.getElementById(`item-total-${productId}`);
                    if (itemTotalEl) itemTotalEl.innerText = formatMoney(data.itemTotal) + ' FCFA';

                    // Update Seller Subtotals (might be multiple on page if rendering bug, better to loop grouped items eventually if needed. For now assuming structured right)
                    // We need seller group id to perfectly map it. But easiest way is fetching all subtotal spans and mapping if we assigned them correctly. 
                    // To do this perfectly we need to ensure the group loop has access to sellerId. We added ID: seller-subtotal-sellerId.
                    // Wait, our backend doesn't output which SellerId was changed. Let's fix that or rely on page reload on failure. 
                    // Let's just reload page if totals get out of sync, or we can just update the specific seller subtotal if we returned it. 
                    // Actually, simpler: reload the page structure since updating quantities in multiple carts might be tricky.
                    // Wait, we DO return sellerSubtotal. We just need to find the closest wrapper.
                    let rowWrapper = input.closest('.premium-card');
                    if (rowWrapper) {
                        let subtotalSpan = rowWrapper.querySelector('[id^="seller-subtotal-"]');
                        if (subtotalSpan) subtotalSpan.innerText = formatMoney(data.sellerSubtotal) + ' FCFA';
                    }

                    // Update Cart Totals
                    document.querySelectorAll('.cart-subtotal').forEach(el => el.innerText = formatMoney(data
                        .cartTotal) + ' FCFA');
                    document.querySelectorAll('.cart-total').forEach(el => el.innerText = formatMoney(data.cartTotal) +
                        ' FCFA');
                    document.querySelectorAll('.cart-btn-total').forEach(el => el.innerText = formatMoney(data
                        .cartTotal));
                } else {
                    // Revert input gracefully
                    input.value = currentQty;
                }
            } catch (error) {
                console.error("Erreur de mise à jour: ", error);
                input.value = currentQty; // Revert
            }
        }
    </script>
@endsection
