

@extends('layouts.layoutMaster')

@section('title', 'Espace Boutique')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Vendeur /</span> Espace Boutique
</h4>

@if(session('status'))
<div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('status') === 'product-created' ? 'Produit ajouté avec succès !' : (session('status') === 'product-updated' ? 'Produit mis à jour !' : 'Produit supprimé.') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Header Boutique -->
<div class="card mb-4">
    <div class="user-profile-header-banner">
        <div class="banner-overlay"></div>
    </div>
    <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
            <img src="{{ $shopLogoUrl }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" style="border: 5px solid #fff; width: 120px;">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5 map-card-content">
            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                <div class="user-profile-info">
                    <h4>{{ $seller->shop_name }}</h4>
                    <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                        <li class="list-inline-item">
                            <i class='ti ti-calendar me-1'></i> Licence expire le {{ $seller->formatted_license_expiry }}
                        </li>
                    </ul>
                </div>
                <x-feature-link feature="seller.products.create" route="{{ route('seller.products.create') }}" class="btn btn-primary">
                    <i class='ti ti-plus me-1'></i> Ajouter un Produit
                </x-feature-link>
            </div>
        </div>
    </div>
</div>

<!-- Liste des Produits -->
<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Mes Produits</h5>
    </div>
    <x-feature-section feature="seller.products.view-list" showDeniedMessage="true">
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $product->thumbnail_url }}" alt="Img" class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-heading text-truncate" style="max-width: 200px;">{{ $product->title }}</span>
                                <small class="text-muted">{{ $product->type === 'service' ? 'Service' : 'Bien' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->category->name ?? 'Non classé' }}</td>
                    <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($product->stock_quantity <= 0 && $product->type === 'physical_good')
                            <span class="badge bg-label-danger">Rupture</span>
                        @else
                            {{ $product->stock_quantity }}
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-label-success">Actif</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('seller.products.show', $product->id) }}" class="text-body" title="Voir"><i class="ti ti-eye ti-sm me-2"></i></a>
                            <x-feature-link feature="seller.products.edit" route="{{ route('seller.products.edit', $product->id) }}" class="text-body" title="Modifier">
                                <i class="ti ti-edit ti-sm me-2"></i>
                            </x-feature-link>
                            <form id="delete-form-{{ $product->id }}" action="{{ route('seller.products.destroy', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-feature-button feature="seller.products.destroy" type="button" onclick="confirmDelete('{{ $product->id }}')" class="btn btn-icon btn-text-secondary rounded-pill waves-effect">
                                    <i class="ti ti-trash ti-sm"></i>
                                </x-feature-button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <i class="ti ti-box-off ti-xl mb-2 text-muted"></i>
                            <p>Aucun produit dans votre boutique pour le moment.</p>
                            <a href="{{ route('seller.products.create') }}" class="btn btn-sm btn-outline-primary">Ajouter mon premier produit</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </x-feature-section>
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
