<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reçu de Commande #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #5F61E6;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .meta {
            width: 100%;
            margin-bottom: 30px;
        }

        .meta table {
            width: 100%;
        }

        .meta td {
            vertical-align: top;
            padding: 5px;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items th {
            background: #f8f9fa;
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .items td {
            padding: 10px;
            border: 1px solid #eee;
        }

        .total-section {
            text-align: right;
            margin-top: 20px;
        }

        .total-row {
            font-size: 1.2em;
            font-weight: bold;
            color: #5F61E6;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #777;
            border-top: 1px dashed #ddd;
            padding-top: 15px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .bg-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .bg-warning {
            background-color: #fff3cd;
            color: #664d03;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.1;
            z-index: -1000;
            width: 80%;
            text-align: center;
        }

        .watermark img {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ public_path('assets/img/branding/logo.png') }}" alt="Watermark">
    </div>
    <div class="header">
        <div class="logo">{{ config('app.name', 'MarketPlace') }}</div>
        <div class="title">REÇU DE PAIEMENT</div>
        <p>Preuve d'achat électronique</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="50%">
                    <strong>Client :</strong><br>
                    {{ $order->buyer->name }}<br>
                    {{ $order->buyer->email }}
                </td>
                <td width="50%" style="text-align: right;">
                    <strong>Référence :</strong> #{{ substr($order->id, 0, 8) }}<br>
                    <strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Méthode :</strong>
                    @if ($order->payment_method == 'wallet')
                        Portefeuille
                    @elseif($order->payment_method == 'cash_on_delivery')
                        Cash à la livraison
                    @else
                        {{ $order->payment_method }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <strong>Vendeur :</strong>
        {{ $order->items->first()->product->seller->shop_name ?? 'Boutique Certifiée' }}
        @if ($order->items->first()->product->seller->phone)
            <br>Tél: {{ $order->items->first()->product->seller->phone }}
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix Unitaire</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->title }}</td>
                    <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price * $item->quantity, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <p>Sous-total : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
        <p class="total-row">Total Net Payé : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>

        <div style="margin-top: 10px;">
            Statut :
            Statut :
            @if ($order->delivery_status == 'delivered' || $order->status == 'delivered')
                <span class="badge bg-success">PAYÉ & LIVRÉ</span>
            @elseif($order->status == 'paid')
                <span class="badge bg-success">PAYÉ (En cours de livraison)</span>
            @elseif($order->status == 'pending_payment')
                <span class="badge bg-warning">EN ATTENTE DE PAIEMENT</span>
            @else
                <span class="badge bg-warning">EN COURS</span>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance !</p>
        <p>Pour toute question concernant cette commande, veuillez contacter le vendeur ou le support
            {{ config('app.name') }}.</p>
    </div>
</body>

</html>
