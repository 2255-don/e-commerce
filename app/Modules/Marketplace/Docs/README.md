# Marketplace Module

## Description
Module e-commerce complet incluant produits, catégories, panier et gestion de commandes.

---

## 🏗️ Architecture

### Value Objects
- `Price` - Gestion prix avec arithmétique
- `Stock` - Gestion stock avec disponibilité
- `SKU` - Identifiant unique produit
- `Slug` - Identifiant URL-friendly
- `ProductType` - Enum (physical, digital, service)
- `OrderStatus` - Enum (pending, processing, completed, cancelled)
- `PaymentMethod` - Enum (wallet, cash_on_delivery, mobile_money)

### Entities
- **Product** (Aggregate Root)
  - decreaseStock(), increaseStock()
  - isInStock(), updatePrice()
  - activate/deactivate()
  - calculateTotal()
  
- **Category** (Hiérarchique)
  - parent/children relationships
  - isRoot(), hasChildren()
  
- **Cart** (Aggregate Root)
  - addItem(), removeItem(), updateQuantity()
  - clear(), getTotalAmount()
  - validate() - Vérifier stock disponible
  
- **Order** (Aggregate Root)
  - markAsPaid(), markAsProcessing(), markAsCompleted()
  - cancel(reason)
  - canBeCancelled(), canBeRefunded()

### Services
- **CartService** - Gestion panier (add, remove, update, clear)
- **OrderService** - Checkout complet avec intégration Fintech
- **StockService** - Gestion inventaire (reserve/release)
- **PricingService** - Calculs de prix

---

## 📡 Routes

### Web Routes
```
GET  /marketplace                  - Liste produits
GET  /products/{slug}              - Détail produit
GET  /marketplace/search           - Recherche

# Cart (Auth required)
GET  /cart                         - Voir panier
POST /cart/add                     - Ajouter au panier
PUT  /cart/{itemId}                - M.A.J quantité
DELETE /cart/{itemId}              - Supprimer item

# Checkout
GET  /checkout                     - Page checkout
POST /checkout/process             - Traiter commande
```

### API Routes
```
GET  /api/marketplace/products           - Liste produits
GET  /api/marketplace/products/{id}      - Détail
POST /api/marketplace/products/search    - Recherche

# Cart API (Auth required)
GET  /api/marketplace/cart               - Voir panier
POST /api/marketplace/cart/add           - Ajouter
PUT  /api/marketplace/cart/{itemId}      - M.A.J
DELETE /api/marketplace/cart/{itemId}    - Supprimer

# Orders API
GET  /api/marketplace/orders             - Mes commandes
POST /api/marketplace/orders             - Créer commande
GET  /api/marketplace/orders/{id}        - Détail
```

---

## 🔧 Utilisation

### Ajouter au panier
```php
use Modules\Marketplace\Services\CartService;
use Modules\Marketplace\DTOs\AddToCartDTO;

$cartService = app(CartService::class);

$dto = AddToCartDTO::fromRequest($request->all(), $userId);
$cart = $cartService->addToCart($dto);
```

### Passer une commande
```php
use Modules\Marketplace\Services\OrderService;
use Modules\Marketplace\DTOs\PlaceOrderDTO;

$orderService = app(OrderService::class);

$dto = PlaceOrderDTO::fromRequest($request->all(), $userId);
$order = $orderService->placeOrder($dto);

// Traiter paiement wallet
$orderService->processPayment($order->id, 'wallet');
```

###  Filtrer produits
```php
use Modules\Marketplace\Interfaces\ProductRepositoryInterface;
use Modules\Marketplace\DTOs\ProductFilterDTO;

$productRepo = app(ProductRepositoryInterface::class);

$filters = ProductFilterDTO::fromRequest([
    'category_id' => 'xxx',
    'min_price' => 1000,
    'max_price' => 50000,
    'search' => 'laptop',
]);

$products = $productRepo->search($filters, 15);
```

---

## 🔗 Intégrations Modules

### Identity Module
- **User** Entity pour seller, buyer relationships

### Fintech Module
- **WalletService** pour paiements
- **TransactionReference** pour ordre references

---

## ⚡ Events

- `ProductCreated` - Nouveau produit créé
- `ProductStockUpdated` - Stock modifié
- `OrderPlaced` - Commande passée
- `OrderCompleted` - Commande complétée

---

## 📝 Notes

- Stock automatiquement réservé lors du checkout
- Validation stock avant ajout panier
- Release stock si commande annulée
- Intégration Fintech pour paiements wallet
- Support multi-seller

---

**Version**: 1.0.0  
**DDD Phase 3** ✅
