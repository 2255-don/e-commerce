

@extends('layouts/layoutMaster')

@section('title', 'Mes Commandes')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Mon Compte /</span> Historique des Commandes
</h4>

<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Mes Achats</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut Livraison</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                    $detailRoute = ($order->status === 'pending' || $order->status === 'pending_payment') 
                                    ? route('user.orders.show_pending', $order->id) 
                                    : route('user.orders.show', $order->id);
                @endphp
                <tr>
                    <td>
                        <a href="{{ $detailRoute }}">#{{ substr($order->id, 0, 8) }}</a>
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($order->delivery_status === 'delivered')
                            <span class="badge bg-label-success">Livré</span>
                        @else
                            <span class="badge bg-label-warning">En cours</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ $detailRoute }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                            <i class="ti ti-eye"></i>
                        </a>
                        @if($order->delivery_status === 'delivered')
                            <a href="{{ route('user.orders.download', $order->id) }}" class="btn btn-sm btn-icon btn-text-primary rounded-pill" title="Télécharger Reçu">
                                <i class="ti ti-download"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="ti ti-shopping-cart-x ti-xl mb-3 text-muted"></i>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-primary">Découvrir la boutique</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->count() > 0)
    <div class="card-footer">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
