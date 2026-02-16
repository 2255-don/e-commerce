@if (empty($cart))
    <div class="text-center py-5">
        <i class="ti ti-shopping-cart-off ti-xl mb-3 text-muted"></i>
        <h5>Votre panier est vide</h5>
        <a href="{{ route('marketplace.index') }}" class="btn btn-primary mt-3" data-bs-dismiss="offcanvas">Commencer les
            achats</a>
    </div>
@else
    <ul class="list-group list-group-flush mb-3">
        @foreach ($cart as $sellerId => $group)
            @foreach ($group['items'] as $item)
                <li class="list-group-item d-flex justify-content-between align-items-center px-0 mb-2">
                    <div class="d-flex align-items-center">
                        <img src="{{ $item['image'] }}" class="rounded me-3"
                            style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $item['title'] }}</h6>
                            <small class="text-muted">{{ $item['quantity'] }} x
                                {{ number_format($item['price'], 0, ',', ' ') }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="d-block fw-semibold">{{ number_format($item['subtotal'], 0, ',', ' ') }}
                            FCFA</span>
                        <form action="{{ route('cart.remove', $item['id']) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-icon btn-sm text-danger border-0 bg-transparent p-0">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        @endforeach
    </ul>

    <div class="border-top pt-3">
        <div class="d-flex justify-content-between mb-3">
            <span class="fw-bold">Total</span>
            <span class="fw-bold text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary w-100 mb-2">Voir le panier</a>
        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Commander</a>
    </div>
@endif
