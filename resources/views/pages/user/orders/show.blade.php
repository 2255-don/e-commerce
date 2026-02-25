@php
    $configData = Helper::appClasses();
@endphp

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@extends('layouts/layoutMaster')

@section('title', 'Détails Commande #' . $order->delivery_code)

@section('page-style')
    <style>
        :root {
            --brand-navy: #0f172a;
            --brand-gold: #d4af37;
            --brand-gold-light: #f3e5ab;
            --brand-gold-hover: #b5952f;
        }

        .text-brand-gold {
            color: var(--brand-gold) !important;
        }

        .bg-brand-navy {
            background-color: var(--brand-navy) !important;
            color: white;
        }

        .border-brand-gold {
            border-color: var(--brand-gold) !important;
        }

        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .card-header-premium {
            background: linear-gradient(135deg, var(--brand-navy) 0%, #1e293b 100%);
            color: white;
            padding: 1.5rem;
        }

        .btn-premium {
            background-color: #d4af37 !important;
            border-color: #d4af37 !important;
            color: #0f172a !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-premium:hover {
            background-color: #b5952f !important;
            border-color: #b5952f !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        /* Stepper Styling */
        .tracking-wrapper {
            padding: 2rem 0;
        }

        .tracking-item {
            position: relative;
            padding-top: 20px;
            text-align: center;
        }

        .tracking-item .tracking-icon {
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #64748b;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .tracking-item.active .tracking-icon {
            background-color: var(--brand-gold);
            color: var(--brand-navy);
            box-shadow: 0 0 0 5px rgba(212, 175, 55, 0.2);
        }

        .tracking-item::before {
            content: '';
            position: absolute;
            top: 40px;
            left: -50%;
            width: 100%;
            height: 3px;
            background-color: #e2e8f0;
            z-index: 1;
        }

        .tracking-item:first-child::before {
            display: none;
        }

        .tracking-item.active::before {
            background-color: var(--brand-gold);
        }

        .tracking-date {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }

        .tracking-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.25rem;
            color: var(--brand-navy);
        }

        /* Product Item */
        .order-item-card {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }

        .order-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
    </style>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Mon Compte /</span> Commande #{{ $order->delivery_code }}
    </h4>



    <div class="row">
        <!-- Order Progress & Details -->
        <div class="col-lg-8 mb-4">

            <!-- Tracking Card -->
            <div class="card mb-4">
                <div class="card-header-premium">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 text-white">Suivi de Commande</h5>
                            <small class="text-white-50">Réf: {{ $order->reference }}</small>
                        </div>
                        <span class="badge bg-white text-dark">{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row tracking-wrapper">
                        <!-- Step 1: Placed -->
                        <div class="col tracking-item active">
                            <div class="tracking-icon"><i class="ti ti-shopping-cart"></i></div>
                            <div class="tracking-title">Validée</div>
                            <div class="tracking-date">{{ $order->created_at->format('H:i') }}</div>
                        </div>

                        <!-- Step 2: Shipped -->
                        <div
                            class="col tracking-item {{ in_array($order->delivery_status, ['shipped', 'dispute', 'delivered']) ? 'active' : '' }}">
                            <div class="tracking-icon"><i class="ti ti-truck"></i></div>
                            <div class="tracking-title">Expédiée</div>
                            @if ($order->delivery_status != 'pending')
                                <div class="tracking-date">En route</div>
                            @endif
                        </div>

                        <!-- Dispute Handling Visualization -->
                        @if ($order->delivery_status == 'dispute')
                            <div class="col tracking-item active">
                                <div class="tracking-icon bg-danger text-white"><i class="ti ti-gavel"></i></div>
                                <div class="tracking-title text-danger">Litige</div>
                                <div class="tracking-date">En cours</div>
                            </div>
                        @else
                            <!-- Dummy last step for visual balance if not dispute -->
                        @endif

                        <!-- Step 3: Delivered -->
                        <div class="col tracking-item {{ $order->delivery_status == 'delivered' ? 'active' : '' }}">
                            <div class="tracking-icon"><i class="ti ti-package"></i></div>
                            <div class="tracking-title">Livrée</div>
                            @if ($order->delivery_status == 'delivered')
                                <div class="tracking-date">Reçu</div>
                            @endif
                        </div>


                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="mb-0 text-brand-navy">Articles Commandés</h5>
                </div>
                <div class="card-body pt-4">
                    @foreach ($order->items as $item)
                        <div class="order-item-card d-flex align-items-center">
                            <img src="{{ $item->product->thumbnail_url }}" alt="Img" class="order-item-img me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-brand-navy">{{ $item->product->title }}</h6>
                                <p class="mb-0 text-muted small">
                                    Vendeur: <span
                                        class="fw-medium text-brand-gold">{{ $sellerProfile->shop_name ?? 'Boutique' }}</span>
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold mb-1">x{{ $item->quantity }}</div>
                                <div class="text-brand-navy fw-bold">
                                    {{ number_format($item->unit_price * $item->quantity, 0, ',', ' ') }} FCFA</div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span class="fs-5 text-muted">Total Payé</span>
                        <span class="fs-4 fw-bold text-brand-gold">{{ number_format($order->total_amount, 0, ',', ' ') }}
                            FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4 mb-4">

            <!-- Secret Code Card -->
            <div class="card mb-4 bg-brand-navy text-white text-center">
                <div class="card-body">
                    <div class="mb-2"><i class="ti ti-lock-open fs-1 text-brand-gold"></i></div>
                    <h6 class="text-white-50 mb-1">Code de Livraison</h6>
                    <h2 class="text-brand-gold mb-0 letter-spacing-2">{{ $order->delivery_code }}</h2>
                    <small class="text-white-50 mt-2 d-block">Communiquez ce code au livreur uniquement à la
                        réception.</small>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Actions Requises</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="d-grid gap-3">
                        @if ($order->delivery_status !== 'delivered' && $order->delivery_status !== 'dispute')
                            <div class="alert alert-warning mb-0 border-0 bg-label-warning">
                                <div class="d-flex">
                                    <i class="ti ti-info-circle me-2 mt-1"></i>
                                    <p class="mb-0 small">Avez-vous bien reçu votre colis ? Confirmez pour libérer le
                                        paiement au vendeur.</p>
                                </div>
                            </div>

                            <form id="confirm-delivery-form" action="{{ route('user.orders.confirm', $order->id) }}"
                                method="POST">
                                @csrf
                                <button type="button" class="btn btn-premium w-100 py-2" onclick="confirmDelivery()">
                                    <i class="ti ti-check me-2"></i> Je Confirme la Réception
                                </button>
                            </form>

                            <button type="button" class="btn btn-label-danger w-100" onclick="reportIssue()">
                                <i class="ti ti-alert-triangle me-2"></i> Signaler un problème
                            </button>

                            <form id="report-issue-form" action="{{ route('user.orders.refund', $order->id) }}"
                                method="POST" class="d-none">
                                @csrf
                            </form>
                        @elseif($order->delivery_status === 'dispute')
                            <div class="alert alert-danger mb-0 text-center">
                                <i class="ti ti-gavel fs-3 mb-2"></i>
                                <h6 class="alert-heading">Litige en cours</h6>
                                <p class="mb-0 small">Dossier transmis au support.</p>
                            </div>
                        @else
                            <div class="alert alert-success bg-label-success mb-0 border-0 text-center py-4">
                                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3"
                                    style="width: 60px; height: 60px;">
                                    <i class="ti ti-circle-check text-success fs-2"></i>
                                </div>
                                <h5 class="text-success mb-1">Commande Livrée</h5>
                                <p class="text-success mb-0 small">Merci pour votre confiance !</p>
                            </div>
                            <a href="{{ route('user.orders.download', $order->id) }}"
                                class="btn btn-outline-success w-100 mt-3">
                                <i class="ti ti-file-download me-2"></i> Télécharger Reçu (PDF)
                            </a>
                        @endif

                        <a href="{{ route('user.orders.index') }}" class="btn btn-light w-100 mt-2">
                            Retour à mes commandes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        function reportIssue() {
            Swal.fire({
                title: 'Signaler un problème',
                text: "Cette action ouvrira un litige. L'équipe support examinera votre dossier. Confirmer ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, signaler',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('report-issue-form').submit();
                }
            })
        }

        function confirmDelivery() {
            Swal.fire({
                title: 'Confirmation de Réception',
                text: "Certifiez-vous avoir reçu l'intégralité de la commande ?",
                icon: 'question',
                showCancelButton: true,
                showDenyButton: false,
                confirmButtonText: 'Oui, j\'ai tout reçu',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-success me-3',
                    cancelButton: 'btn btn-label-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('confirm-delivery-form').submit();
                }
            })
        }
    </script>
@endsection
