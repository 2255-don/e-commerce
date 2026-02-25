@extends('layouts/layoutMaster')

@section('title', $isEdit ? 'Modifier Produit' : 'Nouveau Produit')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-style')
    <style>
        /* ── Palette JOUAN-SUGU ── */
        :root {
            --js-gold: #C9A84C;
            --js-gold-light: #E2C97E;
            --js-gold-dark: #9A7A2E;
            --js-silver: #B0B8C1;
        }

        /* ── Page header ── */
        .form-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .form-page-header .breadcrumb-block {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .form-page-header .accent-bar {
            width: 4px;
            height: 28px;
            border-radius: 2px;
            background: linear-gradient(180deg, var(--js-gold), var(--js-gold-dark));
            flex-shrink: 0;
        }

        .form-page-header h4 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3040;
            margin: 0;
        }

        .form-page-header h4 span {
            color: #aaa;
            font-weight: 400;
        }

        /* ── Mode badge ── */
        .mode-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem 1rem;
            border-radius: 50px;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .mode-badge.create {
            background: rgba(201, 168, 76, .12);
            color: var(--js-gold-dark);
            border: 1px solid rgba(201, 168, 76, .3);
        }

        .mode-badge.edit {
            background: rgba(99, 102, 241, .1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, .25);
        }

        /* ── Error alert ── */
        .error-alert {
            background: rgba(234, 84, 85, .07);
            border: 1px solid rgba(234, 84, 85, .25);
            border-left: 4px solid #ea5455;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            animation: slideDown .3s ease;
        }

        .error-alert ul {
            margin: 0;
            padding-left: 1.25rem;
            color: #c0392b;
            font-size: .875rem;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Form layout ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 900px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ── Form cards ── */
        .form-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, .06);
            box-shadow: 0 2px 16px rgba(0, 0, 0, .06);
            overflow: hidden;
            transition: box-shadow .25s ease;
        }

        .form-card:focus-within {
            box-shadow: 0 4px 24px rgba(201, 168, 76, .12);
        }

        .form-card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, .06);
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .form-card-header .card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(201, 168, 76, .15), rgba(201, 168, 76, .06));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--js-gold-dark);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .form-card-header h6 {
            font-size: .9rem;
            font-weight: 700;
            color: #2c3040;
            margin: 0;
        }

        .form-card-body {
            padding: 1.5rem;
        }

        /* ── Form controls ── */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-group:last-child {
            margin-bottom: 0;
        }

        .field-label {
            display: block;
            font-size: .8rem;
            font-weight: 700;
            color: #5a6475;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .45rem;
        }

        .field-label .required {
            color: #ea5455;
            margin-left: .2rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px !important;
            border: 1.5px solid rgba(0, 0, 0, .1) !important;
            font-size: .875rem !important;
            padding: .6rem .9rem !important;
            transition: border-color .2s, box-shadow .2s !important;
            color: #2c3040 !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--js-gold) !important;
            box-shadow: 0 0 0 3px rgba(201, 168, 76, .15) !important;
            outline: none !important;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .field-hint {
            font-size: .75rem;
            color: #9aa0ad;
            margin-top: .35rem;
        }

        /* ── Image upload zone ── */
        .upload-zone {
            border: 2px dashed rgba(201, 168, 76, .4);
            border-radius: 12px;
            padding: 2rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all .25s ease;
            background: rgba(201, 168, 76, .03);
            position: relative;
        }

        .upload-zone:hover,
        .upload-zone.drag-over {
            border-color: var(--js-gold);
            background: rgba(201, 168, 76, .07);
        }

        .upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-zone .upload-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(201, 168, 76, .15), rgba(201, 168, 76, .06));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto .75rem;
            font-size: 1.4rem;
            color: var(--js-gold);
            transition: transform .2s ease;
        }

        .upload-zone:hover .upload-icon {
            transform: scale(1.1);
        }

        .upload-zone p {
            font-size: .85rem;
            color: #8a92a0;
            margin: 0;
        }

        .upload-zone strong {
            color: var(--js-gold-dark);
        }

        /* ── Image preview grid ── */
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: .6rem;
            margin-top: 1rem;
        }

        .preview-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 1;
            border: 2px solid rgba(201, 168, 76, .2);
            animation: popIn .25s ease both;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(.85);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(234, 84, 85, .9);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            opacity: 0;
            transition: opacity .2s;
            z-index: 2;
        }

        .preview-item:hover .remove-btn {
            opacity: 1;
        }

        /* ── Existing images ── */
        .existing-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: .6rem;
            margin-top: .75rem;
        }

        .existing-img-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 1;
            border: 2px solid rgba(201, 168, 76, .25);
        }

        .existing-img-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .existing-img-item .del-btn {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(234, 84, 85, .9);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            opacity: 0;
            transition: opacity .2s;
            z-index: 2;
        }

        .existing-img-item:hover .del-btn {
            opacity: 1;
        }

        /* ── Type toggle ── */
        .type-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .5rem;
        }

        .type-option {
            position: relative;
        }

        .type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .type-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .4rem;
            padding: .85rem .5rem;
            border-radius: 10px;
            border: 1.5px solid rgba(0, 0, 0, .1);
            cursor: pointer;
            transition: all .2s ease;
            font-size: .8rem;
            font-weight: 600;
            color: #8a92a0;
            text-align: center;
        }

        .type-option label i {
            font-size: 1.3rem;
        }

        .type-option input:checked+label {
            border-color: var(--js-gold);
            background: rgba(201, 168, 76, .08);
            color: var(--js-gold-dark);
            box-shadow: 0 0 0 3px rgba(201, 168, 76, .12);
        }

        .type-option label:hover {
            border-color: var(--js-gold-light);
            color: var(--js-gold-dark);
        }

        /* ── Stock field (hidden for service) ── */
        #stock-field {
            transition: opacity .3s ease, max-height .3s ease;
        }

        #stock-field.hidden {
            opacity: 0;
            pointer-events: none;
            max-height: 0;
            overflow: hidden;
            margin: 0;
        }

        /* ── Price input with currency ── */
        .price-input-wrapper {
            position: relative;
        }

        .price-input-wrapper .currency-tag {
            position: absolute;
            right: .9rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: .75rem;
            font-weight: 700;
            color: var(--js-gold-dark);
            background: rgba(201, 168, 76, .1);
            padding: .2rem .5rem;
            border-radius: 5px;
            pointer-events: none;
        }

        .price-input-wrapper input {
            padding-right: 4.5rem !important;
        }

        /* ── Footer actions ── */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .75rem;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, .06);
            background: #fafbfc;
            border-radius: 0 0 16px 16px;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--js-gold) 0%, var(--js-gold-light) 50%, var(--js-gold) 100%);
            background-size: 200% 100%;
            color: #1A1D23 !important;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            padding: .6rem 1.6rem;
            letter-spacing: .03em;
            transition: background-position .4s ease, box-shadow .3s ease, transform .2s ease;
            box-shadow: 0 4px 16px rgba(201, 168, 76, .35);
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-gold:hover {
            background-position: 100% 0;
            box-shadow: 0 6px 24px rgba(201, 168, 76, .5);
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: transparent;
            border: 1.5px solid rgba(0, 0, 0, .12);
            border-radius: 10px;
            padding: .6rem 1.4rem;
            color: #6c757d;
            font-weight: 600;
            font-size: .875rem;
            transition: all .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-cancel:hover {
            background: rgba(0, 0, 0, .04);
            border-color: rgba(0, 0, 0, .2);
            color: #3d4455;
        }

        /* Dark mode */
        [data-bs-theme="dark"] .form-card,
        [data-bs-theme="dark"] .form-footer {
            background: #2c3040;
            border-color: rgba(255, 255, 255, .07);
        }

        [data-bs-theme="dark"] .form-card-header {
            border-color: rgba(255, 255, 255, .07);
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background: #252836 !important;
            border-color: rgba(255, 255, 255, .1) !important;
            color: #c8cdd8 !important;
        }

        [data-bs-theme="dark"] .form-footer {
            background: #252836;
        }

        [data-bs-theme="dark"] .form-page-header h4 {
            color: #e4e8f0;
        }

        [data-bs-theme="dark"] .type-option label {
            border-color: rgba(255, 255, 255, .1);
            color: #8a92a0;
        }
    </style>
@endsection

@section('content')

    {{-- ── Page Header ── --}}
    <div class="form-page-header">
        <div class="breadcrumb-block">
            <div class="accent-bar"></div>
            <h4>
                <span>Vendeur / Espace Boutique /</span>
                {{ $isEdit ? 'Modifier' : 'Ajouter' }} un Produit
            </h4>
        </div>
        <span class="mode-badge {{ $isEdit ? 'edit' : 'create' }}">
            <i class="ti ti-{{ $isEdit ? 'edit' : 'plus' }}"></i>
            {{ $isEdit ? 'Mode édition' : 'Nouveau produit' }}
        </span>
    </div>


    {{-- ── Form ── --}}
    <form method="POST"
        action="{{ $isEdit ? route('seller.products.update', $product->id) : route('seller.products.store') }}"
        enctype="multipart/form-data" id="productForm">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="form-grid">

            {{-- ── LEFT: Main Info ── --}}
            <x-feature-section feature="seller.products.form-basic-info">
                <div class="d-flex flex-column gap-4">

                    {{-- Title & Description --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="card-icon"><i class="ti ti-file-description"></i></div>
                            <h6>Informations générales</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="field-group">
                                <label class="field-label" for="title">Titre du produit <span
                                        class="required">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title', $product->title ?? '') }}" placeholder="Ex: Smartphone XYZ Pro"
                                    required>
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"
                                    placeholder="Décrivez votre produit en détail : caractéristiques, avantages, utilisation…">{{ old('description', $product->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Images --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="card-icon"><i class="ti ti-photo"></i></div>
                            <h6>Images du produit</h6>
                        </div>
                        <div class="form-card-body">

                            {{-- Existing images (edit mode) --}}
                            @if ($isEdit && $product->images->count() > 0)
                                <div class="field-group">
                                    <label class="field-label">Images actuelles</label>
                                    <div class="existing-images-grid">
                                        @foreach ($product->images as $img)
                                            <div class="existing-img-item">
                                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Image produit">
                                                <button type="button" class="del-btn"
                                                    onclick="confirmDeleteImage('{{ $img->id }}')" title="Supprimer">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="field-hint">Cliquez sur × pour supprimer une image existante.</p>
                                </div>
                            @endif

                            {{-- Upload zone --}}
                            <div class="upload-zone" id="uploadZone">
                                <input type="file" id="product_images" name="product_images[]" multiple accept="image/*"
                                    {{ !$isEdit ? 'required' : '' }} onchange="handleFileSelect(this)">
                                <div class="upload-icon"><i class="ti ti-cloud-upload"></i></div>
                                <p><strong>Cliquez ou glissez</strong> vos images ici</p>
                                <p style="margin-top:.3rem;font-size:.75rem">JPG, PNG · Max 8 MB au total</p>
                            </div>

                            {{-- Preview --}}
                            <div class="preview-grid" id="previewGrid"></div>
                        </div>
                    </div>

                </div>
            </x-feature-section>

            {{-- ── RIGHT: Specs ── --}}
            <x-feature-section feature="seller.products.form-specs">
                <div class="d-flex flex-column gap-4">

                    {{-- Type --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="card-icon"><i class="ti ti-tag"></i></div>
                            <h6>Type de produit</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="type-toggle">
                                <div class="type-option">
                                    <input type="radio" id="type_physical" name="type" value="physical_good"
                                        {{ old('type', $product->type ?? 'physical_good') == 'physical_good' ? 'checked' : '' }}
                                        onchange="toggleStockField(this.value)">
                                    <label for="type_physical">
                                        <i class="ti ti-package"></i>
                                        Bien physique
                                    </label>
                                </div>
                                <div class="type-option">
                                    <input type="radio" id="type_service" name="type" value="service"
                                        {{ old('type', $product->type ?? '') == 'service' ? 'checked' : '' }}
                                        onchange="toggleStockField(this.value)">
                                    <label for="type_service">
                                        <i class="ti ti-tools"></i>
                                        Service
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing & Stock --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="card-icon"><i class="ti ti-coins"></i></div>
                            <h6>Prix & Stock</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="field-group">
                                <label class="field-label" for="price">Prix <span class="required">*</span></label>
                                <div class="price-input-wrapper">
                                    <input type="number" class="form-control" id="price" name="price"
                                        value="{{ old('price', $product->price ?? '') }}" min="0" placeholder="0"
                                        required>
                                    <span class="currency-tag">FCFA</span>
                                </div>
                            </div>

                            <div class="field-group" id="stock-field">
                                <label class="field-label" for="stock_quantity">Quantité en stock <span
                                        class="required">*</span></label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity"
                                    value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}" min="0"
                                    placeholder="0">
                                <p class="field-hint">Laissez vide si illimité (service).</p>
                            </div>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="card-icon"><i class="ti ti-category"></i></div>
                            <h6>Catégorie</h6>
                        </div>
                        <div class="form-card-body">
                            <div class="field-group">
                                <label class="field-label" for="category_id">Catégorie <span
                                        class="required">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Choisir une catégorie…</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </x-feature-section>

        </div>{{-- /form-grid --}}

        {{-- ── Footer ── --}}
        <div class="form-footer mt-4"
            style="border-radius:16px;border:1px solid rgba(0,0,0,.06);box-shadow:0 2px 16px rgba(0,0,0,.06);">
            <a href="{{ route('seller.dashboard') }}" class="btn-cancel">
                <i class="ti ti-arrow-left"></i> Annuler
            </a>
            <button type="submit" class="btn-gold">
                <i class="ti ti-{{ $isEdit ? 'device-floppy' : 'plus' }}"></i>
                {{ $isEdit ? 'Enregistrer les modifications' : 'Créer le produit' }}
            </button>
        </div>

    </form>

    {{-- Hidden delete-image forms --}}
    @if ($isEdit)
        @foreach ($product->images as $img)
            <form id="delete-img-{{ $img->id }}" action="{{ route('seller.products.images.destroy', $img->id) }}"
                method="POST" style="display:none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

@endsection

@section('page-script')
    <script>
        /* ── Image preview ── */
        let selectedFiles = [];

        function handleFileSelect(input) {
            const files = Array.from(input.files);
            let totalSize = files.reduce((s, f) => s + f.size, 0);

            if (totalSize > 8 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fichiers trop volumineux',
                    text: `Poids total : ${(totalSize/1024/1024).toFixed(2)} MB. Limite : 8 MB.`,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        popup: 'rounded-4'
                    },
                    buttonsStyling: false
                });
                input.value = '';
                return;
            }

            selectedFiles = files;
            renderPreviews();
        }

        function renderPreviews() {
            const grid = document.getElementById('previewGrid');
            grid.innerHTML = '';
            selectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    item.innerHTML = `
                <img src="${e.target.result}" alt="${file.name}">
                <button type="button" class="remove-btn" onclick="removePreview(${idx})" title="Retirer">
                    <i class="ti ti-x"></i>
                </button>`;
                    grid.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        }

        function removePreview(idx) {
            selectedFiles.splice(idx, 1);
            // Rebuild DataTransfer to update the input
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            document.getElementById('product_images').files = dt.files;
            renderPreviews();
        }

        /* ── Drag & drop ── */
        const zone = document.getElementById('uploadZone');
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            const input = document.getElementById('product_images');
            const dt = new DataTransfer();
            Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
            input.files = dt.files;
            handleFileSelect(input);
        });

        /* ── Stock field toggle ── */
        function toggleStockField(type) {
            const field = document.getElementById('stock-field');
            const stockInput = document.getElementById('stock_quantity');
            if (type === 'service') {
                field.classList.add('hidden');
                stockInput.removeAttribute('required');
            } else {
                field.classList.remove('hidden');
                stockInput.setAttribute('required', '');
            }
        }

        // Init on load
        (function() {
            const checked = document.querySelector('input[name="type"]:checked');
            if (checked) toggleStockField(checked.value);
        })();

        /* ── Delete existing image ── */
        function confirmDeleteImage(imageId) {
            Swal.fire({
                title: 'Supprimer cette image ?',
                text: 'Elle sera définitivement supprimée.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary',
                    popup: 'rounded-4'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-img-' + imageId).submit();
                }
            });
        }
    </script>
@endsection
