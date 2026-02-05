# Seller Module

## Description
Module vendeur complet incluant profils vendeurs, gestion licence, produits et commandes.

---

## 🏗️ Architecture

### Value Objects
- `LicenseNumber` - Numéro licence auto-généré (LIC-YYYYMMDD-XXXXXXXX)
- `BusinessName` - Nom entreprise avec validation
- `SellerStatus` - Enum (pending, approved, suspended, rejected)
- `CommissionRate` - Taux commission avec calculs

### Entities
- **SellerProfile** (Aggregate Root)
  - approve(), suspend(reason), reject(reason)
  - generateLicense(), updateLicense()
  - isActive(), canSell()
  - calculateCommission(amount)

### Services
- **SellerProfileService** - Gestion profils (create, update, approve, suspend, reject)
- **SellerStatsService** - Statistiques vendeur (products, orders, revenue, commission)

---

## 📡 Routes

### Web Routes
```
# Seller Dashboard (Auth + Seller middleware)
GET  /seller/dashboard              - Dashboard vendeur

# Products Management
GET  /seller/products               - Liste produits
GET  /seller/products/create        - Formulaire création
POST /seller/products               - Créer produit
GET  /seller/products/{id}/edit     - Formulaire édition
PUT  /seller/products/{id}          - M.A.J produit
POST /seller/products/{id}/stock    - M.A.J stock rapide

# Orders
GET  /seller/orders                 - Commandes vendeur
GET  /seller/orders/{id}            - Détail commande
```

### API Routes
```
# Profile API (Auth required)
GET  /api/seller/profile            - Mon profil
POST /api/seller/profile            - Créer profil
PUT  /api/seller/profile            - M.A.J profil
GET  /api/seller/profile/stats      - Mes stats

# Products API
GET  /api/seller/products           - Mes produits
POST /api/seller/products           - Créer produit
PUT  /api/seller/products/{id}      - M.A.J produit
POST /api/seller/products/{id}/stock - M.A.J stock
```

---

## 🔧 Utilisation

### Créer un profil vendeur
```php
use Modules\Seller\Services\SellerProfileService;
use Modules\Seller\DTOs\CreateSellerProfileDTO;

$sellerService = app(SellerProfileService::class);

$dto = CreateSellerProfileDTO::fromRequest($request->all(), $userId);
$profile = $sellerService->createProfile($dto);
// Licence auto-générée
```

### Approuver un vendeur (Admin)
```php
$profile = $sellerService->approveProfile($profileId);
// Status devient 'approved', is_active = true
```

### Obtenir stats vendeur
```php
use Modules\Seller\Services\SellerStatsService;

$statsService = app(SellerStatsService::class);
$stats = $statsService->getStats($sellerId);
// Returns: total_products, total_orders, total_revenue, commission_earned, net_revenue
```

---

## 🔗 Intégrations Modules

### Identity Module
- **User** Entity pour SellerProfile::user()

### Marketplace Module
- **Product** Entity pour gestion produits vendeur
- **Order** Entity pour commandes vendeur
- **ProductRepository**, **OrderRepository** pour queries

---

## ⚡ Workflow Vendeur

1. **Demande** - User crée profil vendeur (status: pending)
2. **Licence** - Licence auto-générée (expire dans 1 an)
3. **Approval** - Admin approve/reject demande
4. **Vente** - Si approved: peut créer produits
5. **Gestion** - Manage stock, view commandes, stats
6. **Commission** - Calculée automatiquement sur ventes

---

## 📝 Business Rules

- Profile doit être `approved` pour vendre
- Licence doit être valide (non-expirée)
- `canSell()` = approved + active + license valid
- Seller peut modifier que ses propres produits
- Commission par défaut: 10%
- Suspension avec raison obligatoire

---

**Version**: 1.0.0  
**DDD Phase 4** ✅
