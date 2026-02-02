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

@section('title', 'Détails Commande #' . substr($order->id, 0, 8))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Mon Compte / Commandes /</span> #{{ substr($order->id, 0, 8) }}
</h4>

<div class="row">
    <!-- Order Details -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0">Détails de la commande</h5>
                <span class="badge bg-label-{{ $order->delivery_status === 'delivered' ? 'success' : 'warning' }}">
                    {{ $order->delivery_status === 'delivered' ? 'Livré' : 'En cours' }}
                </span>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between mb-4">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Date de commande:</dt>
                            <dd class="col-sm-8">{{ $order->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-4">Statut:</dt>
                            <dd class="col-sm-8">
                                @if($order->status == 'completed')
                                    <span class="badge bg-success">Terminée</span>
                                @elseif($order->status == 'pending_payment')
                                    <span class="badge bg-warning">En attente de paiement (COD)</span>
                                @else
                                    <span class="badge bg-primary">En cours</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Livraison:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-label-{{ $order->delivery_status == 'delivered' ? 'success' : 'warning' }}">
                                    {{ $order->delivery_status == 'delivered' ? 'Livré' : 'En attente' }}
                                </span>
                            </dd>
                            
                            <dt class="col-sm-4">Code de Livraison:</dt>
                            <dd class="col-sm-8"><span class="fw-bold text-danger">{{ $order->delivery_code }}</span> (À donner au livreur)</dd>
                        </dl>
                </div>
                
                <hr class="my-4">

                <h6 class="mb-3">Produits</h6>
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td style="width: 80px;">
                                    <img src="{{ $item->product->thumbnail_url }}" alt="Img" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $item->product->title }}</span>
                                        <small class="text-muted">Vendeur: {{ $sellerProfile->shop_name ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td class="text-center">x{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price * $item->quantity, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Payé</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header border-bottom">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body pt-4">
                <div class="d-grid gap-3">
                    @if($order->delivery_status !== 'delivered')
                        <div class="alert alert-warning mb-0">
                            <h6 class="alert-heading mb-1"><i class="ti ti-alert-circle me-1"></i> Confirmation</h6>
                            <p class="mb-0 small">Ne confirmez la réception que si vous avez physiquement reçu tous les produits de cette commande.</p>
                        </div>
                        <form id="confirm-delivery-form" action="{{ route('user.orders.confirm', $order->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-success w-100" onclick="confirmDelivery()">
                                <i class="ti ti-check me-2"></i> Confirmer la Réception
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0 d-flex align-items-center">
                            <i class="ti ti-circle-check me-2 fs-5"></i>
                            <div>
                                <h6 class="mb-0">Commande Livrée</h6>
                                <small>Le {{ $order->updated_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        <a href="{{ route('user.orders.download', $order->id) }}" class="btn btn-primary w-100">
                            <i class="ti ti-file-download me-2"></i> Télécharger Reçu (PDF)
                        </a>
                    @endif
                    
                    <a href="{{ route('user.orders.index') }}" class="btn btn-label-secondary w-100">
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
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
