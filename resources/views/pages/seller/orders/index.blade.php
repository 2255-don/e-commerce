@extends('layouts/layoutMaster')

@section('title', 'Mes Commandes Clients')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
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

        .text-gold {
            color: #D4AF37 !important;
        }

        .bg-gold {
            background-color: #D4AF37 !important;
            color: #fff;
        }

        .bg-navy {
            background-color: #1a1a1a !important;
            color: #fff;
        }

        .btn-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #E5C158 0%, #D4AF37 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.3);
            color: #fff;
        }

        .order-row {
            transition: background-color 0.2s ease;
        }

        .order-row:hover {
            background-color: rgba(212, 175, 55, 0.03);
        }

        .product-thumb {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #eee;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeInDown">
            <div>
                <h4 class="fw-bold mb-1" style="color: #1a1a1a;">
                    <span class="text-muted fw-light">Espace Vendeur /</span> Mes Commandes
                </h4>
                <p class="mb-0 text-muted">Gérez les commandes reçues et suivez les expéditions.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-label-primary p-2">
                    <i class="ti ti-package me-1"></i> {{ $orders->total() }} Commandes
                </span>
            </div>
        </div>


        <div class="premium-card animate__animated animate__fadeInUp">
            <div class="card-datatable table-responsive">
                <table class="table table-hover">
                    <thead class="bg-navy">
                        <tr>
                            <th class="text-white">Ref Commande</th>
                            <th class="text-white">Client</th>
                            <th class="text-white">Produits Achetés</th>
                            <th class="text-white">Total</th>
                            <th class="text-white">Statut</th>
                            <th class="text-white text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($orders as $order)
                            <tr class="order-row">
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold fs-6">#{{ $order->delivery_code }}</span>
                                        <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                                {{ strtoupper(substr($order->buyer->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-heading">{{ $order->buyer->name }}</span>
                                            <small class="text-muted">{{ $order->buyer->phone_number ?? 'Expert' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($order->items as $item)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->thumbnail_url }}" class="product-thumb me-2"
                                                    alt="Product">
                                                <div>
                                                    <span class="d-block text-heading text-truncate"
                                                        style="max-width: 150px;">{{ $item->product->title }}</span>
                                                    <small class="text-muted">Qté: {{ $item->quantity }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="fw-bold text-gold fs-6">{{ number_format($order->total_amount, 0, ',', ' ') }}
                                        FCFA</span>
                                </td>
                                <td>
                                    @if ($order->delivery_status == 'pending')
                                        <span class="badge bg-label-warning me-1">En Attente</span>
                                    @elseif($order->delivery_status == 'shipped')
                                        <span class="badge bg-label-info me-1">Expédiée</span>
                                    @elseif($order->delivery_status == 'delivered')
                                        <span class="badge bg-label-success me-1">Livrée</span>
                                    @elseif($order->delivery_status == 'cancelled')
                                        <span class="badge bg-label-danger me-1">Annulée</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($order->delivery_status == 'pending' && $order->status != 'cancelled')
                                        <form id="ship-form-{{ $order->id }}"
                                            action="{{ route('seller.orders.ship', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-gold"
                                                onclick="confirmShip('{{ $order->id }}', '{{ $order->delivery_code }}')">
                                                <i class="ti ti-truck-delivery me-1"></i> Expédier
                                            </button>
                                        </form>
                                    @elseif($order->delivery_status == 'shipped')
                                        <button class="btn btn-sm btn-label-info cursor-default" disabled>
                                            <i class="ti ti-clock me-1"></i> En Route
                                        </button>
                                    @elseif($order->delivery_status == 'delivered')
                                        <button class="btn btn-sm btn-label-success cursor-default" disabled>
                                            <i class="ti ti-check me-1"></i> Terminé
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <span class="badge bg-label-secondary p-3 rounded-circle">
                                                <i class="ti ti-package-off fs-2"></i>
                                            </span>
                                        </div>
                                        <h5 class="mb-1">Aucune commande reçue</h5>
                                        <p class="text-muted mb-0">Vos commandes apparaîtront ici une fois que les clients
                                            achèteront vos produits.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-top p-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        function confirmShip(orderId, orderCode) {
            Swal.fire({
                title: 'Expédier la commande #' + orderCode + ' ?',
                text: "Confirmez que le colis est prêt à être remis au service de livraison ou au client.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, expédier !',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-gold me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('ship-form-' + orderId).submit();
                }
            });
        }
    </script>
@endsection
