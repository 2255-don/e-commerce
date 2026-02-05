# Fintech Module

## Description
Module de gestion financière incluant portefeuilles électroniques, transactions, et paiements Mobile Money.

---

## 🏗️ Architecture

### Value Objects
- `Amount` - Montant avec arithmétique et validation
- `Currency` - Devise (FCFA, USD, EUR)
- `TransactionReference` - Référence unique de transaction
- `TransactionType` - Type de transaction (recharge, transfer, payment, refund)
- `TransactionStatus` - Statut (pending, processing, completed, failed)
- `KycStatus` - Statut KYC (none, pending, verified, rejected)

### Entities
- **Wallet** (Aggregate Root)
  - credit(Amount)
  - debit(Amount)
  - hasEnoughBalance(Amount)
  - activate/deactivate()
  
- **Transaction**
  - markAsCompleted()
  - markAsFailed(reason)
  - cancel()
  - Status transitions

### Services
- **WalletService** - Operations wallet (recharge, transfer, debit, credit)
- **TransactionService** - Création et gestion transactions
- **PaymentGatewayService** - Intégration Mobile Money

---

## 📡 API Endpoints

### Web Routes
```
GET  /wallet/recharge              - Formulaire rechargement
POST /wallet/process-recharge      - Traiter rechargement
GET  /wallet/transactions          - Historique transactions
```

### API Routes
```
GET  /api/wallet/balance                    - Balance wallet
GET  /api/wallet/transactions               - Liste transactions
POST /api/wallet/transfer                   - Transfert fonds
GET  /api/wallet/transactions/{reference}   - Transaction par référence
```

---

## 🔧 Utilisation

### Recharger un Wallet
```php
use Modules\Fintech\Services\WalletService;
use Modules\Fintech\DTOs\RechargeWalletDTO;

$walletService = app(WalletService::class);

$dto = RechargeWalletDTO::fromRequest($request->all(), $userId);
$transaction = $walletService->rechargeWallet($dto, $transactionRef);
```

### Transférer des Fonds
```php
use Modules\Fintech\Services\WalletService;
use Modules\Fintech\DTOs\TransferDTO;

$dto = TransferDTO::create(
    senderWalletId: $senderWallet->id,
    receiverWalletId: $receiverWallet->id,
    amount: 1000,
    description: 'Paiement commande'
);

$transaction = $walletService->transferFunds($dto);
```

---

## ⚡ Events

- `WalletCredited` - Émis lors du crédit d'un wallet
- `WalletDebited` - Émis lors du débit d'un wallet
- `TransactionCompleted` - Émis quand transaction complétée

---

## 🔗 Dépendances

### Module Identity
- `User` Entity pour relation Wallet -> User

---

## 📝 Notes

- Mobile Money actuellement **mockée** pour développement
- Support multi-providers: Airtel, Vodacom, Orange, Africell
- Tous les montants en FCFA par défaut
- Transactions atomiques avec rollback sur erreur

---

**Version**: 1.0.0  
**DDD Phase 2** ✅
