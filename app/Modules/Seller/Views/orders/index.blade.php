@extends('layouts.layoutMaster')

@section('title', 'Commandes reçues')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Vendeur /</span> Commandes
    </h4>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total (votre part)</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->reference ?? $order->id }}</td>
                        <td>{{ $order->buyer->name ?? 'Client inconnu' }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                        <td><span class="badge bg-label-primary">{{ $order->status }}</span></td>
                        <td>
                            <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                                <i class="ti ti-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">Aucune commande reçue.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
