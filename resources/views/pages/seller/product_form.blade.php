

@extends('layouts/layoutMaster')

@section('title', $isEdit ? 'Modifier Produit' : 'Nouveau Produit')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Vendeur / Espace Boutique /</span> {{ $isEdit ? 'Modifier' : 'Ajouter' }} un Produit
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">Informations du Produit</h5>
            
            @if ($errors->any())
            <div class="card-body">
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ $isEdit ? route('seller.products.update', $product->id) : route('seller.products.store') }}" enctype="multipart/form-data">
                @csrf
                @if($isEdit) @method('PUT') @endif
                
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Info -->
                        <x-feature-section feature="seller.products.form-basic-info">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titre du produit</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $product->title ?? '') }}" placeholder="Ex: Smartphone XYZ" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description détaillée...">{{ old('description', $product->description ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="product_images" class="form-label">Images du produit</label>
                                    <input class="form-control" type="file" id="product_images" name="product_images[]" multiple accept="image/*" {{ !$isEdit ? 'required' : '' }}>
                                    <div class="form-text">Vous pouvez sélectionner plusieurs images (JPG, PNG).</div>
                                </div>
                                
                                @if($isEdit && $product->images->count() > 0)
                                    <div class="mb-3">
                                        <label class="form-label d-block">Images actuelles :</label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach($product->images as $img)
                                                <div class="position-relative border rounded p-1" style="width: 100px; height: 100px;">
                                                    <img src="{{ asset('storage/' . $img->image_path) }}" class="w-100 h-100 rounded" style="object-fit: cover;">
                                                    <button type="button" class="btn btn-icon btn-xs btn-danger position-absolute top-0 end-0 translate-middle shadow-sm rounded-pill" 
                                                            onclick="confirmDeleteImage('{{ $img->id }}')">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Supprimez les images obsolètes avant d'en ajouter de nouvelles.</div>
                                    </div>
                                @endif
                            </div>
                        </x-feature-section>

                        <!-- Side Specs -->
                        <x-feature-section feature="seller.products.form-specs">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Choisir...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Prix (FCFA)</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price ?? '') }}" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="physical_good" {{ (old('type', $product->type ?? '') == 'physical_good') ? 'selected' : '' }}>Bien Physique</option>
                                    <option value="service" {{ (old('type', $product->type ?? '') == 'service') ? 'selected' : '' }}>Service</option>
                                </select>
                            </div>
                        </div>
                        </x-feature-section>
                    </div>
                </div>
                
                <div class="card-footer border-top d-flex justify-content-end">
                    <a href="{{ route('seller.dashboard') }}" class="btn btn-label-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Mettre à jour' : 'Créer le Produit' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if($isEdit)
    @foreach($product->images as $img)
        <form id="delete-img-{{ $img->id }}" action="{{ route('seller.products.images.destroy', $img->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endif
@endsection

@section('page-script')
<script>
document.getElementById('product_images').addEventListener('change', function() {
    const files = this.files;
    let totalSize = 0;
    const maxFileSize = 2 * 1024 * 1024; // 2MB per file (default safe limit)
    const maxTotalSize = 8 * 1024 * 1024; // 8MB total (default safe limit)
    
    // Safe limit matching standard server config (8MB) since overrides might fail
    const safeLimit = 8 * 1024 * 1024; 

    for (let i = 0; i < files.length; i++) {
        totalSize += files[i].size;
    }

    if (totalSize > safeLimit) {
        Swal.fire({
            icon: 'error',
            title: 'Fichiers trop volumineux',
            text: 'Le poids total des images (' + (totalSize/1024/1024).toFixed(2) + ' MB) dépasse la limite (8 MB).',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
        this.value = ''; // Reset input
    }
});

function confirmDeleteImage(imageId) {
    Swal.fire({
        title: 'Supprimer cette image ?',
        text: "Elle sera définitivement supprimée.",
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: false,
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        customClass: {
            confirmButton: 'btn btn-danger me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-img-' + imageId).submit();
        }
    });
}
</script>
@endsection
