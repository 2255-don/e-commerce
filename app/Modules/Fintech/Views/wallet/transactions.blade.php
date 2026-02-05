@extends('layouts.layoutMaster')

@section('title', 'Mes Transactions')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
@endsection

@section('content')
<div class="page-header-brand animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title-brand">
                <i class='bx bxs-receipt text-brand-gold'></i>
                Historique Transactions
            </h2>
            <p class="page-subtitle-brand">Consultez toutes vos transactions</p>
        </div>
        <div>
            <a href="{{ route('wallet.index') }}" class="btn btn-brand-primary">
                <i class='bx bx-plus-circle'></i>
                Recharger
            </a>
        </div>
    </div>
</div>

<!-- Balance Card -->
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card-stat animate-fade-in-up" style="border: 2px solid var(--brand-gold-light);">
            <div class="text-center">
                <div class="stat-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem;">
                    <i class='bx bxs-wallet'></i>
                </div>
                <p class="stat-label">Balance Actuelle</p>
                <h3 class="stat-value">{{ $balance->format() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card-brand animate-fade-in-up" style="animation-delay: 0.1s;">
    <div class="card-header card-header-brand" style="background: var(--gradient-gold); color: white;">
        <h5 class="mb-0">
            <i class='bx bx-history'></i>
            Historique
        </h5>
    </div>
    <div class="card-body p-0">
        @if($transactions->isEmpty())
        <div class="text-center py-5">
            <i class='bx bx-receipt' style="font-size: 4rem; opacity: 0.3;"></i>
            <p class="text-muted mt-2">Aucune transaction pour le moment</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-brand mb-0">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>
                            <code class="text-brand-gold">{{ $transaction->reference }}</code>
                        </td>
                        <td>{{ $transaction->type_label }}</td>
                        <td>
                            <strong class="{{ $transaction->isCredit() ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->isCredit() ? '+' : '-' }}{{ $transaction->formatted_amount }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge {{ $transaction->status_badge_class }}">
                                {{ $transaction->status_label }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
