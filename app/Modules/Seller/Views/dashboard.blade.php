@extends('layouts.layoutMaster')

@section('title', 'Tableau de bord Vendeur')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Vendeur /</span> Tableau de bord
    </h4>

    <div class="row">
        <!-- Stats -->
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Ventes Totales</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ number_format($stats['total_sales'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class='ti ti-currency-dollar ti-sm'></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Commandes</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['total_orders'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-secondary rounded p-2">
                                <i class='ti ti-shopping-cart ti-sm'></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Produits Actifs</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $stats['active_products'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded p-2">
                                <i class='ti ti-package ti-sm'></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Dernières Commandes</h5>
                    <a href="{{ route('seller.orders.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-4">Aucune commande récente.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
