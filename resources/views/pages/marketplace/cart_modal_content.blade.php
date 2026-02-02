@if(empty($cart))
    <div class="text-center py-5">
        <i class="ti ti-shopping-cart-off fs-1 text-muted mb-3"></i>
        <p>Votre panier est vide.</p>
        <a href="{{ route('marketplace.index') }}" class="btn btn-primary btn-sm">Commencer mes achats</a>
    </div>
@else
    <div class="cart-items-list" style="max-height: calc(100vh - 250px); overflow-y: auto;">
        @foreach($cart as $sellerId => $group)
            <div class="seller-group mb-4 border-bottom pb-2">
                <div class="d-flex align-items-center mb-2 bg-lighter p-2 rounded">
                    <i class="ti ti-building-store me-2 text-primary"></i>
                    <strong class="text-small text-uppercase">{{ $group['shop_name'] }}</strong>
                </div>
                
                @foreach($group['items'] as $item)
                    <div class="d-flex align-items-center mb-3 position-relative">
                        <div class="flex-shrink-0 me-3">
                            <img src="{{ $item['image'] ?? 'https://via.placeholder.com/50' }}" 
                                 alt="{{ $item['title'] }}" 
                                 class="rounded border" width="60" height="60" style="object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-truncate" style="max-width: 180px;">{{ $item['title'] }}</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $item['quantity'] }} x {{ number_format($item['price'], 0, ',', ' ') }}</small>
                                <span class="fw-bold text-primary">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span>
                            </div>
                        </div>
                        <button class="btn btn-icon btn-sm text-danger remove-item-btn ms-2" 
                                data-url="{{ route('checkout.remove', $item['id']) }}">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                @endforeach
                
                <div class="text-end text-small text-muted mt-1">
                    Sous-total : <strong>{{ number_format($group['subtotal'], 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        @endforeach
    </div>

    <div class="offcanvas-footer border-top p-3 bg-lighter position-absolute bottom-0 start-0 w-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fs-5">Total</span>
            <span class="fs-4 fw-bold text-primary">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg custom-gold-btn">
                <i class="ti ti-check me-2"></i> Commander
            </a>
        </div>
    </div>
@endif
