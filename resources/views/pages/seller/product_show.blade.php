

@extends('layouts/layoutMaster')

@section('title', 'Détails du Produit')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Vendeur / Espace Boutique /</span> {{ $product->title }}
</h4>

<div class="row">
    <!-- Image Gallery -->
    <div class="col-md-6 col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <!-- Main Image -->
                <div class="mb-3 text-center">
                    <img src="{{ $product->thumbnail_url }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain;">
                </div>
                
                <!-- Thumbnails grid -->
                @if($product->images->count() > 1)
                <div class="d-flex gap-2 overflow-auto py-2">
                    @foreach($product->images as $img)
                        <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank" class="border rounded p-1">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="col-md-6 col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informations</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="productAction" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="productAction">
                        <a class="dropdown-item" href="{{ route('seller.products.edit', $product->id) }}">Modifier</a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="productAction">
                        <a class="dropdown-item" href="{{ route('seller.products.edit', $product->id) }}">Modifier</a>
                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('{{ $product->id }}')">Supprimer</button>
                        <form id="delete-form-{{ $product->id }}" action="{{ route('seller.products.destroy', $product->id) }}" method="POST" style="display: none;">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-label-primary me-2">{{ $product->category->name ?? 'Non classé' }}</span>
                    <span class="badge bg-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }}">
                        {{ $product->stock_quantity > 0 ? 'En Stock: ' . $product->stock_quantity : 'Rupture de stock' }}
                    </span>
                </div>
                
                <h3 class="mb-1">{{ number_format($product->price, 0, ',', ' ') }} FCFA</h3>
                <p class="text-muted mb-4">{{ $product->type === 'service' ? 'Service' : 'Bien Physique' }}</p>

                <div class="mb-4">
                    <h6 class="fw-semibold">Description</h6>
                    <p class="text-body">{{ $product->description ?? 'Aucune description disponible.' }}</p>
                </div>

                <div class="divider">
                    <div class="divider-text">Statistiques</div>
                </div>
                
                <div class="d-flex justify-content-between text-muted">
                    <span>Ajouté le :</span>
                    <span class="fw-semibold">{{ $product->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="card-footer border-top text-end">
                <a href="{{ route('seller.dashboard') }}" class="btn btn-label-secondary">Retour à la boutique</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    function confirmDelete(productId) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + productId).submit();
            }
        })
    }
</script>
@endsection
