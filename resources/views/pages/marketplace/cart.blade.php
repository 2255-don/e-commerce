

@extends('layouts/layoutMaster')

@section('title', 'Mon Panier')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Boutique /</span> Mon Panier
</h4>

<div class="row">
    <div class="col-lg-8">
        @if(empty($cart))
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-shopping-cart-off ti-xl mb-3 text-muted"></i>
                    <h5>Votre panier est vide</h5>
                    <a href="{{ route('marketplace.index') }}" class="btn btn-primary mt-3">Commencer les achats</a>
                </div>
            </div>
        @else
            @foreach($cart as $sellerId => $group)
                <div class="card mb-4">
                    <div class="card-header border-bottom bg-lighter">
                        <h6 class="mb-0">Vendu par : <strong>{{ $group['shop_name'] }}</strong></h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                @foreach($group['items'] as $item)
                                <tr>
                                    <td style="width: 100px;">
                                        <img src="{{ $item['image'] }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <h6 class="mb-1 text-truncate" style="max-width: 200px;">{{ $item['title'] }}</h6>
                                        <small class="text-muted">{{ number_format($item['price'], 0, ',', ' ') }} FCFA / unité</small>
                                    </td>
                                    <td style="width: 150px;">
                                        <form action="{{ route('checkout.update', $item['id']) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['max_stock'] }}" class="form-control form-control-sm text-center me-2" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('checkout.remove', $item['id']) }}" class="btn btn-icon btn-text-danger rounded-pill"><i class="ti ti-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="3" class="text-end">Sous-total :</td>
                                    <td class="text-end fw-bold">{{ number_format($group['subtotal'], 0, ',', ' ') }} FCFA</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Résumé</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Sous-total</span>
                    <span>{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span>Livraison</span>
                    <span class="text-success">Gratuit</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold">Total à payer</span>
                    <span class="fw-bold text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>

                @if(!empty($cart))
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Méthode de paiement</label>
                            <select name="type" class="form-select">
                                <option value="wallet">Mon Portefeuille (Solde actuel)</option>
                                <option value="cash_on_delivery">Paiement à la livraison (Cash)</option>
                                <option value="mobile_money" disabled>Mobile Money (Bientôt)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm mb-3">Valider la commande</button>
                    </form>
                @endif
                
                <a href="{{ route('marketplace.index') }}" class="btn btn-label-secondary w-100">
                    <i class="ti ti-arrow-left me-1"></i> Retour à la boutique
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
