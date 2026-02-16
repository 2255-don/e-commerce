@extends('layouts.layoutMaster')

@section('title', 'Détails Commande')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Commandes /</span> #{{ $order->reference ?? $order->id }}
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Articles commandés</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Qté</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->title }}</td>
                                <td>{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal, 0, ',', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Info Client</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nom:</strong> {{ $order->buyer->name }}</p>
                    <p><strong>Email:</strong> {{ $order->buyer->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
