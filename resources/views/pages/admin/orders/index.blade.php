@extends('layouts/layoutMaster')

@section('title', 'Gestion des Commandes')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">Toutes les Commandes</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Vendeur/Boutique</th>
                        <th>Montant</th>
                        <th>Statut Paiement</th>
                        <th>Statut Livraison</th>
                        <th>Actions</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->delivery_code }}</strong></td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ $order->buyer->profile_photo_url }}" alt="Avatar"
                                                class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $order->buyer->name }}</span>
                                        <small class="text-muted">{{ $order->buyer->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $firstItem = $order->items->first();
                                    $sellerUser = $firstItem ? $firstItem->product->sellerUser : null;
                                    $shopName = $firstItem ? $firstItem->product->seller->shop_name : 'Inconnu';
                                @endphp
                                @if ($sellerUser)
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $shopName }}</span>
                                            <small class="text-muted">{{ $sellerUser->name }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Inconnu</span>
                                @endif
                            </td>
                            <td><span class="fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                @if ($order->status == 'pending')
                                    <span class="badge bg-label-warning">En Séquestre</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-label-success">Payé au Vendeur</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-label-danger">Remboursé</span>
                                @else
                                    <span class="badge bg-label-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($order->delivery_status == 'pending')
                                    <span class="badge bg-label-warning">En Attente</span>
                                @elseif($order->delivery_status == 'shipped')
                                    <span class="badge bg-label-info">Expédiée</span>
                                @elseif($order->delivery_status == 'delivered')
                                    <span class="badge bg-label-success">Livrée</span>
                                @elseif($order->delivery_status == 'cancelled')
                                    <span class="badge bg-label-danger">Annulée</span>
                                @elseif($order->delivery_status == 'dispute')
                                    <span class="badge bg-label-danger">Litige</span>
                                @endif
                            </td>
                            <td>
                                @if ($order->delivery_status == 'dispute')
                                    <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST"
                                        onsubmit="return confirm('Voulez-vous vraiment rembourser cette commande ? Cette action est irréversible.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Rembourser</button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucune commande trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
