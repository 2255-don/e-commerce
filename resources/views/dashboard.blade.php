@extends('layouts.layoutMaster')

@section('title', 'Dashboard - Jouan-Sugu')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('page-style')
    <!-- Boxicons -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    
    <style>
        /* Dashboard Custom Styles */
        .dashboard-header {
            margin-bottom: 2rem;
        }

        .welcome-card {
            background: var(--gradient-gold);
            border-radius: var(--radius-xl);
            padding: 2rem;
            color: white;
            box-shadow: var(--shadow-gold-lg);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, 30px) rotate(5deg); }
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            opacity: 0.95;
            font-size: 1rem;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .quick-action-btn {
            padding: 0.65rem 1.5rem;
            border-radius: var(--radius-full);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            transition: all var(--transition-base);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quick-action-btn:hover {
            background: white;
            color: var(--brand-gold);
            transform: translateY(-2px);
        }

        .chart-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--grey-200);
            transition: all var(--transition-smooth);
        }

        .chart-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .chart-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--grey-100);
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--grey-900);
        }

        .recent-activity-item {
            padding: 1rem;
            border-radius: var(--radius-md);
            background: var(--grey-50);
            margin-bottom: 0.75rem;
            transition: all var(--transition-fast);
        }

        .recent-activity-item:hover {
            background: var(--gradient-gold-subtle);
            transform: translateX(5px);
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('content')
    <!-- Welcome Card -->
    <div class="welcome-card animate-fade-in-up">
        <div class="welcome-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="welcome-title">
                        Bienvenue, {{ auth()->user()->name }} ! 👋
                    </h1>
                    <p class="welcome-subtitle">
                        Gérez votre boutique, wallet et commandes depuis votre tableau de bord.
                    </p>
                    <div class="quick-actions">
                        @if(auth()->user()->isSeller())
                            <a href="{{ route('seller.products.create') }}" class="quick-action-btn">
                                <i class='bx bx-plus-circle'></i>
                                Nouveau Produit
                            </a>
                        @else
                            <a href="{{ route('marketplace.index') }}" class="quick-action-btn">
                                <i class='bx bxs-store'></i>
                                Marketplace
                            </a>
                        @endif
                        <a href="{{ route('wallet.index') }}" class="quick-action-btn">
                            <i class='bx bxs-wallet'></i>
                            Mon Wallet
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class='bx bxs-dashboard' style="font-size: 8rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Wallet Balance -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-stat animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="stat-label">Balance Wallet</p>
                        <h3 class="stat-value">${{ number_format(auth()->user()->wallet?->balance ?? 0, 2, ',', ' ') }}</h3>
                        <small class="text-success">
                            <i class='bx bx-trending-up'></i>
                            Disponible
                        </small>
                    </div>
                    <div class="stat-icon">
                        <i class='bx bxs-wallet'></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Count -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-stat animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="stat-label">Commandes</p>
                        <h3 class="stat-value">{{ auth()->user()->orders()->count() }}</h3>
                        <small class="text-muted">
                            <i class='bx bx-package'></i>
                            Total
                        </small>
                    </div>
                    <div class="stat-icon" style="background: var(--gradient-grey);">
                        <i class='bx bxs-shopping-bag'></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Items (if applicable) -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-stat animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="stat-label">Panier</p>
                        <h3 class="stat-value">{{ auth()->user()->cart?->items()->count() ?? 0 }}</h3>
                        <small class="text-muted">
                            <i class='bx bx-cart'></i>
                            Articles
                        </small>
                    </div>
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class='bx bxs-cart'></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seller Stats (if seller) -->
        <div class="col-xl-3 col-sm-6">
            @if(auth()->user()->isSeller())
                <div class="card-stat animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label">Produits Vendus</p>
                            <h3 class="stat-value">{{ auth()->user()->sellerProfile?->products()->count() ?? 0 }}</h3>
                            <small class="text-muted">
                                <i class='bx bx-store'></i>
                                En stock
                            </small>
                        </div>
                        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class='bx bxs-package'></i>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-stat animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="stat-label">Statut</p>
                            <h3 class="stat-value" style="font-size: 1.5rem;">
                                {{ auth()->user()->statut === 'active' ? 'Actif' : 'Inactif' }}
                            </h3>
                            <small class="text-success">
                                <i class='bx bx-check-circle'></i>
                                Compte vérifié
                            </small>
                        </div>
                        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class='bx bxs-user-check'></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Charts & Recent Activity -->
    <div class="row g-4">
        <!-- Chart Card (placeholder) -->
        <div class="col-lg-8">
            <div class="chart-card animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class='bx bx-line-chart text-brand-gold'></i>
                        Aperçu des Activités
                    </h5>
                </div>
                <div class="chart-body">
                    <div id="activityChart"></div>
                    <div class="text-center py-5">
                        <i class='bx bx-line-chart' style="font-size: 4rem; color: var(--brand-gold-light); opacity: 0.3;"></i>
                        <p class="text-muted mt-3">Graphiques et statistiques à venir...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4">
            <div class="chart-card animate-fade-in-up" style="animation-delay: 0.6s;">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class='bx bx-time text-brand-gold'></i>
                        Activité Récente
                    </h5>
                </div>
                <div class="chart-body">
                    @forelse(auth()->user()->orders()->latest()->limit(5)->get() as $order)
                        <div class="recent-activity-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong>Commande #{{ $order->id }}</strong>
                                    <div class="text-muted small">
                                        {{ $order->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="badge badge-brand-outline">
                                    ${{ number_format($order->total, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class='bx bx-history' style="font-size: 3rem; color: var(--grey-300);"></i>
                            <p class="text-muted mt-2 mb-0">Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection