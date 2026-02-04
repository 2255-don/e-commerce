# RAPPORT TECHNIQUE - JOUAN-SUGU
**Plateforme E-commerce & Fintech**

---

**Date**: 2 Février 2026  
**Version de l'Application**: 1.0  
**Framework**: Laravel 10+  
**Type**: Plateforme Hybride E-commerce/Fintech

---

## RÉSUMÉ EXÉCUTIF

Jouan-Sugu est une plateforme hybride combinant marketplace e-commerce et services fintech. L'application permet aux utilisateurs d'acheter/vendre des produits tout en gérant leurs finances via un portefeuille digital intégré.

### Statistiques Clés
- **11 Modèles** de données
- **10+ Controllers** organisés par domaine
- **7 Services** métier
- **2 Middleware** personnalisés
- **Architecture MVC** avec pattern Service Layer

---

## 1. ARCHITECTURE GLOBALE

### 1.1 Stack Technique

| Composant | Technologie | Version |
|-----------|-------------|---------|
| Backend | Laravel | 10+ |
| Frontend | Blade + Bootstrap | 4/5 |
| Base de données | MySQL | - |
| Authentification | Laravel Fortify | - |
| Thème Admin | Vuexy | - |
| Icons | Boxicons | - |
| UUID | HasUuids Trait | - |

###1.2 Pattern Architectural

```
┌─────────────┐
│   Routes    │ → Définition des endpoints
└─────────────┘
       ↓
┌─────────────┐
│ Controllers │ → Gestion HTTP + Validation
└─────────────┘
       ↓
┌─────────────┐
│  Services   │ → Logique métier
└─────────────┘
       ↓
┌─────────────┐
│   Models    │ → Eloquent ORM
└─────────────┘
       ↓
┌─────────────┐
│  Database   │ → MySQL
└─────────────┘
```

### 1.3 Organisation Domain-Driven (Refactoring 2026-02-02)

**Architecture organisée par domaines métier** pour améliorer la maintenabilité et scalabilité.

#### Structure Controllers

```
app/Http/Controllers/
├── Web/                      # Controllers Web (retournent views)
│   ├── Marketplace/
│   │   └── MarketplaceController.php
│   ├── Cart/
│   │   └── CheckoutController.php
│   ├── Order/
│   │   └── OrderHistoryController.php
│   ├── User/
│   │   ├── UserController.php
│   │   └── KycController.php
│   ├── Seller/
│   │   ├── SellerController.php (licensing)
│   │   └── ProductController.php (CRUD produits)
│   ├── Wallet/
│   │   └── WalletController.php
│   └── Admin/
│       └── KycController.php
│
└── Api/                      # Controllers API (retournent JSON)
    ├── Auth/
    │   └── AuthApiController.php
    ├── Wallet/
    │   └── WalletApiController.php
    └── Seller/
        └── SellerApiController.php
```

#### Structure Services

```
app/Services/
├── Marketplace/
│   └── ProductService.php
├── Cart/
│   └── CartService.php
├── Order/
│   └── OrderService.php
├── Payment/
│   ├── PaymentService.php
│   └── MobileMoneyService.php
├── User/
│   └── KycService.php
└── Seller/
    └── LicenseService.php
```

#### Avantages de Cette Architecture

1. **Clarté**: Chaque domaine métier est isolé
2. **Scalabilité**: Facile d'ajouter de nouveaux domaines
3. **Maintenance**: Trouver le code est intuitif
4. **Cohérence**: Web et API partagent les mêmes Services
5. **DRY**: Pas de duplication de logique métier
6. **Testabilit é**: Services isolés = tests unitaires faciles

#### Routes Organisées

Les fichiers de routes `web.php` et `api.php` sont organisés avec imports groupés :

```php
// Exemple: routes/web.php
use App\Http\Controllers\Web\Marketplace\MarketplaceController;
use App\Http\Controllers\Web\Cart\CheckoutController;
// ... imports groupés par domaine

Route::prefix('boutique')->name('marketplace.')->group(function () {
    // Routes marketplace groupées
});

Route::prefix('cart')->name('checkout.')->group(function () {
    // Routes cart groupées
});
```

---

## 2. MODULES & FONCTIONNALITÉS

### 2.1 MODULE MARKETPLACE (E-Commerce)

#### 🎯 Objectif
Permettre la navigation et l'achat de produits en ligne.

#### 📁 Fichiers Impliqués

| Type | Fichier | Localisation |
|------|---------|--------------|
| Controller | `MarketplaceController.php` | `app/Http/Controllers/Web/Marketplace/` |
| Models | `Product.php`, `Category.php`, `ProductImage.php` | `app/Models/` |
| Views | `index.blade.php`, `show.blade.php` | `resources/views/pages/marketplace/` |
| Routes | Route::get('/boutique') | `routes/web.php:25-26` |

#### 🔧 Méthodes Principales

**MarketplaceController**
- `index()`: Affiche la liste des produits avec recherche et filtres
  - Paramètres de recherche: `search`, `category`, `min_price`, `max_price`
  - Pagination: 12 produits par page
  - EagerLoading: category, images, seller
  
- `show(Product $product)`: Affiche les détails d'un produit
  - Route model binding automatique
  - Vue publique accessible sans authentification

#### 💡 Logique d'Implémentation

1. **Navigation Libre**: Tout le monde peut consulter les produits
2. **Filtrage Intelligent**: Recherche multicritères (titre, description, prix, catégorie)
3. **Stock Management**: Seuls les produits en stock sont affichés
4. **Image Handling**: Support multi-images avec image primaire

#### ⚙️ Workflow

```
Utilisateur → /boutique
    ↓
MarketplaceController@index
    ↓
Query Builder (filtres + pagination)
    ↓
View avec produits + catégories
```

---

### 2.2 MODULE PANIER & CHECKOUT (E-Commerce)

#### 🎯 Objectif
Gérer le panier d'achat et le processus de commande.

#### 📁 Fichiers Impliqués

| Type | Fichier | Localisation |
|------|---------|--------------|
| Controller | `CheckoutController.php` | `app/Http/Controllers/Web/Cart/` |
| Service | `CartService.php` | `app/Services/Cart/` |
| Service | `OrderService.php` | `app/Services/Order/` |
| Models | `Cart.php`, `CartItem.php`, `Order.php`, `OrderItem.php` | `app/Models/` |
| Views | `cart.blade.php`, `cart_modal_content.blade.php` | `resources/views/pages/marketplace/` |
| Routes | Route::group (auth required) | `routes/web.php:29-35` |

#### 🔧 Méthodes Principales

**CheckoutController**
- `index()`: Affiche le panier groupé par vendeur
- `add($productId)`: Ajoute un produit au panier (support AJAX)
- `update($productId)`: Modifie la quantité
- `remove($productId)`: Retire un produit
- `cartDetails()`: Retourne HTML du panier pour modal (AJAX)
- `process(Request)`: Traite la commande complète

**CartService** (Service Layer)
- `getCartInstance()`: Récupère/crée le panier (user ou session)
- `getCart()`: Retourne le panier formaté
- `add(Product, quantity)`: Logique d'ajout au panier
- `update(productId, quantity)`: Mise à jour quantité
- `remove(productId)`: Suppression item
- `clear()`: Vide le panier
- `total()`: Calcule le total
- `getGroupedBySeller()`: Groupe les items par vendeur (crucial pour multi-seller)

**OrderService** (Service Layer)
- `processCheckout($user, $cartItems, $totalAmount, $paymentMethod)`: Traite toute la commande
  - Validation du solde wallet
  - Déduction du montant
  - Création de transaction
  - Groupement par vendeur
  - Création d'une commande par vendeur
  - Création des items de commande
  - Décrément du stock
  - Transaction database complète

#### 💡 Logique d'Implémentation

1. **Multi-Seller Support**: Les commandes sont automatiquement groupées par vendeur
2. **Guest Cart Support**: Les visiteurs (session_id) conservent leur panier
3. **Wallet-First**: Le paiement passe obligatoirement par le wallet
4. **Atomic Transactions**: DB::transaction() assure la cohérence
5. **Stock Management**: Décrémente automatiquement le stock lors de la commande
6. **Delivery Code**: Chaque commande reçoit un code de livraison unique

#### ⚙️ Workflow Complet

```
1. Utilisateur ajoute au panier
   → CheckoutController@add
   → CartService@add
   → Cart & CartItem créés/mis à jour

2. Utilisateur valide commande
   → CheckoutController@process
   → OrderService@processCheckout
   → Transaction DB {
        Débiter wallet
        Créer transaction
        Grouper par vendeur
        Créer orders (1 par vendeur)
        Créer order_items
        Décrémenter stock
      }
   → CartService@clear
   → Redirect vers orders.pending
```

---

### 2.3 MODULE WALLET (Fintech)

#### 🎯 Objectif
Gérer le portefeuille digital des utilisateurs.

#### 📁 Fichiers Impliqués

| Type | Fichier | Localisation |
|------|---------|--------------|
| Controller | `WalletController.php` | `app/Http/Controllers/Web/Wallet/` |
| Service | `PaymentService.php` | `app/Services/Payment/` |
| Service | `MobileMoneyService.php` | `app/Services/Payment/` |
| Models | `Wallet.php`, `Transaction.php` | `app/Models/` |
| Views | `recharge.blade.php` | `resources/views/pages/wallet/` |
| Routes | Route::group (wallet, auth) | `routes/web.php:19-22` |

#### 🔧 Méthodes Principales

**WalletController**
- `showRecharge()`: Affiche le formulaire de recharge
- `processRecharge(Request)`: Traite la recharge du wallet

**PaymentService**
- `processPayment($senderWallet, $amount, $type, $description)`: Débite un wallet
  - Validation du solde
  - Déduction du montant
  - Création de transaction
  - Transaction BD atomique
  
- `deposit($receiverWallet, $amount, $reference, $description)`: Crédite un wallet
  - Incrémente le solde
  - Crée la transaction de dépôt

#### 💡 Logique d'Implémentation

1. **One Wallet Per User**: Relation hasOne avec User
2. **Bi-directional Transactions**: sender_wallet_id & receiver_wallet_id
3. **Transaction Tracking**: Toutes les opérations sont tracées
4. **Reference Unique**: Chaque transaction a une référence unique (PAY-XXXXXX)
5. **Currency Support**: Champ currency pour multi-devises futures

#### ⚙️ Workflow

```
Recharge Wallet:
User → showRecharge()
  ↓
Formulaire (montant, méthode)
  ↓
processRecharge()
  ↓
MobileMoneyService (simulation)
  ↓
PaymentService@deposit
  ↓
Wallet crédité + Transaction créée
```

---

### 2.4 MODULE KYC (Fintech)

#### 🎯 Objectif
Vérifier l'identité des utilisateurs (conformité fintech).

#### 📁 Fichiers Impliqués

| Type | Fichier | Localisation |
|------|---------|--------------|
| Controller User | `KycController.php` | `app/Http/Controllers/Web/User/` |
| Controller Admin | `KycController.php` (Admin) | `app/Http/Controllers/Web/Admin/` |
| Service | `KycService.php` | `app/Services/User/` |
| Views User | `form.blade.php` | `resources/views/pages/kyc/` |
| Views Admin | `index.blade.php` | `resources/views/pages/admin/kyc/` |
| Routes | Route::group (kyc, auth) | `routes/web.php:63-66, 70-73` |

#### 🔧 Méthodes Principales

**KycController** (Utilisateur)
- `showForm()`: Affiche le formulaire KYC
- `store(Request)`: Soumet les documents KYC
  - Upload de document d'identité
  - Stockage dans storage/app/public/kyc
  - Statut passé à 'pending'

**AdminKycController** (Admin) → renommé `KycController` (namespace Web/Admin)
- `index()`: Liste des demandes KYC (pending)
- `approve(User $user)`: Approuve une demande KYC
  - Statut → 'verified'
  
- `reject(User $user)`: Rejette une demande KYC
  - Statut → 'rejected'

**KycService**
- Logique de validation des documents
- Notification aux utilisateurs

#### 💡 Logique d'Implémentation

1. **Mandatory for Sellers**: KYC vérifié requis pour vendre
2. **Document Upload**: Stockage sécurisé des pièces d'identité
3. **3 Statuts**: pending, verified, rejected
4. **Admin Approval**: Validation manuelle par admin
5. **Middleware Check**: EnsureUserIsActiveSeller vérifie le KYC

#### ⚙️ Workflow

```
User → Soumet KYC
  ↓
KycController@store
  ↓
Document uploadé
  ↓
kyc_status = 'pending'

Admin → Liste KYC
  ↓
AdminKycController@index
  ↓
Approve/Reject
  ↓
kyc_status = 'verified'/'rejected'
  ↓
User peut/ne peut pas vendre
```

---

### 2.5 MODULE SELLER (E-Commerce + Fintech)

#### 🎯 Objectif
Gérer les vendeurs et leurs produits.

#### 📁 Fichiers Impliqués

| Type | Fichier | Localisation |
|------|---------|--------------|
| Controller | `SellerController.php` | `app/Http/Controllers/Web/Seller/` |
| Controller | `ProductController.php` | `app/Http/Controllers/Web/Seller/` |
| Service | `LicenseService.php` | `app/Services/Seller/` |
| Service | `ProductService.php` | `app/Services/Marketplace/` |
| Models | `SellerProfile.php`, `Product.php` | `app/Models/` |
| Middleware | `EnsureUserIsActiveSeller.php` | `app/Http/Middleware/` |
| Views | `license.blade.php`, `dashboard.blade.php`, CRUD products | `resources/views/pages/seller/` |
| Routes | Route::group (seller, auth) | `routes/web.php:50-60` |

#### 🔧 Méthodes Principales

**SellerController**
- `showLicenseForm()`: Affiche la page d'achat de licence
- `purchaseWithWallet()`: Traite l'achat de licence vendeur
  - Débite le wallet (ex: 50 USD)
  - Crée SellerProfile
  - Active la licence (expires_at = +1 an)

**ProductController** (Resource Controller - ex-SellerEspaceBoutiqueController)
- `index()`: Dashboard vendeur avec stats
- `create()`: Formulaire création produit
- `store(Request)`: Créer nouveau produit
  - Upload multi-images
  - Validation stock, prix
  
- `edit(Product $product)`: Formulaire édition
- `update(Request, Product $product)`: Met à jour produit
- `destroy(Product $product)`: Supprime produit
- `destroyImage($productImageId)`: Supprime une image

**ProductService**
- Logique de CRUD produits
- Gestion des images
- Validation métier

**LicenseService**
- `purchaseLicense($user)`: Achat de licence
- `checkLicenseExpiry($sellerProfile)`: Vérifie validité

#### 💡 Logique d'Implémentation

1. **License Required**: Achat de licence obligatoire pour vendre
2. **Wallet Payment**: La licence est payée via wallet
3. **Annual Renewal**: Licence valide 1 an
4. **Multi-Image Products**: Support upload multiple images
5. **Protected Routes**: Middleware EnsureUserIsActiveSeller protège l'espace vendeur
6. **KYC + License**: Deux conditions pour accéder (KYC verified + License active)

#### ⚙️ Workflow

```
Devenir Vendeur:
User → /seller/license
  ↓
Achète licence (50 USD)
  ↓
SellerController@purchaseWithWallet
  ↓
PaymentService débite wallet
  ↓
SellerProfile créé
  ↓
License active 1 an

Gérer Produits:
Seller → /seller/dashboard
  ↓
Middleware: EnsureUserIsActiveSeller
  ↓
↓ (KYC verified + License active?)
  ➤ NON → Redirect /seller/license
  ➤ OUI → SellerEspaceBoutiqueController
```

---

### 2.6 MODULE ORDERS (E-Commerce)

#### 🎯 Objectif
Gérer l'historique et le suivi des commandes.

#### 📁 Fichiers Impliqués

| Type | Fichier | Localisation |
|------|---------|--------------|
| Controller | `OrderHistoryController.php` | `app/Http/Controllers/Web/Order/` |
| Models | `Order.php`, `OrderItem.php` | `app/Models/` |
| Views | `index.blade.php`, `pending.blade.php`, `show.blade.php` | `resources/views/pages/orders/` |
| PDF | `receipt.blade.php` | `resources/views/pdf/` |
| Routes | Route::group (my-orders, auth) | `routes/web.php:38-46` |

#### 🔧 Méthodes Principales

**OrderHistoryController**
- `index()`: Liste des commandes livrées (delivery_status = 'delivered')
- `pending()`: Liste des commandes en attente
- `show(Order $order)`: Détails d'une commande spécifique
- `confirmDelivery(Order $order)`: Confirme la réception
  - Validation du code de livraison
  - Passage à 'delivered'
  - Paiement au vendeur (future feature)
  
- `downloadReceipt(Order $order)`: Télécharge le reçu PDF

#### 💡 Logique d'Implémentation

1. **Status Tracking**: pending, processing, delivered, cancelled
2. **Delivery Code**: Code unique pour confirmer la livraison
3. **Buyer Protection**: Fonds libérés seulement après confirmation
4. **PDF Receipts**: Génération de reçus téléchargeables
5. **Seller Grouping**: Commandes groupées par vendeur

#### ⚙️ Workflow

```
Après Checkout:
Order créée (status = 'pending')
  ↓
Livraison en cours
  ↓
Buyer reçoit produit
  ↓
Buyer confirme (delivery_code)
  ↓
OrderHistoryController@confirmDelivery
  ↓
delivery_status = 'delivered'
  ↓
(Future) Paiement au vendeur
```

---

## 3. MODÈLES DE DONNÉES

### 3.1 User Model

**Fichier**: `app/Models/User.php`

**Traits**: HasFactory, Notifiable, HasUuids, TwoFactorAuthenticatable

**Champs Clés**:
- `id` (UUID)
- `name`, `email`, `password`
- `phone_number`
- `role` (admin, user)
- `kyc_status` (pending, verified, rejected)
- `kyc_document_path`

**Relations**:
- `cart()`: hasOne(Cart)
- `wallet()`: hasOne(Wallet)
- `sellerProfile()`: hasOne(SellerProfile)

**Méthodes**:
- `isSeller()`: Vérifie si vendeur
- `cartItemsCount()`: Compte les articles au panier
- `getProfilePhotoUrlAttribute()`: Génère avatar UI

---

### 3.2 Product Model

**Fichier**: `app/Models/Product.php`

**Champs Clés**:
- `id` (UUID)
- `seller_id`, `category_id`
- `title`, `description`
- `price` (decimal:2)
- `stock_quantity` (integer)
- `type`

**Relations**:
- `seller()`: belongsTo(User)
- `category()`: belongsTo(Category)
- `images()`: hasMany(ProductImage)

**Accessors**:
- `thumbnail_url`: Retourne l'image primaire ou placeholder

---

### 3.3 Order Model

**Fichier**: `app/Models/Order.php`

**Champs Clés**:
- `id` (UUID)
- `buyer_id`
- `total_amount` (decimal:2)
- `status` (pending, processing, delivered, cancelled)
- `payment_method` (wallet, cash_on_delivery)
- `delivery_status` (pending, in_transit, delivered)
- `delivery_code` (6 chars)

**Relations**:
- `buyer()`: belongsTo(User)
- `items()`: hasMany(OrderItem)

---

### 3.4 Wallet Model

**Fichier**: `app/Models/Wallet.php`

**Champs Clés**:
- `id` (UUID)
- `user_id`
- `balance` (decimal)
- `currency` (USD, CDF, EUR)
- `status` (active, frozen)

**Relations**:
- `user()`: belongsTo(User)
- `transactionsAsSender()`: hasMany(Transaction)
- `transactionsAsReceiver()`: hasMany(Transaction)

---

### 3.5 Cart & CartItem Models

**Cart**: Contient le panier (user_id ou session_id)
**CartItem**: Items individuels dans le panier

**Relations**:
- Cart hasMany CartItem
- CartItem belongsTo Product

---

### 3.6 Transaction Model

**Fichier**: `app/Models/Transaction.php`

**Champs Clés**:
- `id` (UUID)
- `sender_wallet_id` (nullable)
- `receiver_wallet_id` (nullable)
- `type` (payment, deposit, transfer)
- `amount` (decimal:2)
- `reference` (unique)
- `description`
- `status` (completed, pending, failed)

---

### 3.7 SellerProfile Model

**Fichier**: `app/Models/SellerProfile.php`

**Champs Clés**:
- `user_id`
- `shop_name`
- `shop_description`
- `license_purchased_at`
- `license_expires_at`
- `is_active` (boolean)

**Méthodes**:
- `isLicenseActive()`: Vérifie si la licence est valide

---

## 4. SERVICES MÉTIER

### 4.1 Vue d'Ensemble

| Service | Responsabilité | Fichier |
|---------|----------------|---------|
| CartService | Gestion du panier | `app/Services/Cart/CartService.php` |
| OrderService | Traitement des commandes | `app/Services/Order/OrderService.php` |
| PaymentService | Transactions financières | `app/Services/Payment/PaymentService.php` |
| ProductService | CRUD produits vendeur | `app/Services/Marketplace/ProductService.php` |
| KycService | Vérification d'identité | `app/Services/User/KycService.php` |
| LicenseService | Gestion licences vendeur | `app/Services/Seller/LicenseService.php` |
| MobileMoneyService | Intégration paiements mobiles | `app/Services/Payment/MobileMoneyService.php` |

### 4.2 Pattern Service Layer

**Objectif**: Séparer la logique métier des controllers pour:
- ✅ Réutilisabilité
- ✅ Testabilité
- ✅ Maintenabilité
- ✅ Single Responsibility Principle

**Exemple de Séparation**:
```php
// Controller: Validation HTTP + Orchestration
CheckoutController@process → OrderService@processCheckout

// Service: Logique métier pure
OrderService {
  - Validation solde
  - Transactions DB
  - Groupement vendeurs
  - Gestion stock
}
```

---

## 5. MIDDLEWARE PERSONNALISÉS

### 5.1 EnsureUserIsActiveSeller

**Fichier**: `app/Http/Middleware/EnsureUserIsActiveSeller.php`

**Objectif**: Protéger les routes vendeur

**Vérifications**:
1. Utilisateur authentifié?
2. SellerProfile existe?
3. Licence active?
4. KYC vérifié?

**Redirect**: Si échec → `/seller/license` avec message d'erreur

**Utilisation**: Route group seller dashboard

---

### 5.2 SetLocale

**Fichier**: `app/Http/Middleware/SetLocale.php`

**Objectif**: Gestion de la langue de l'application

**Logique**:
- Vérifie la session pour la locale
- Applique la locale à l'app

---

## 6. ROUTES & ARCHITECTURE

### 6.1 Routes Publiques

| Route | Controller | Méthode | Description |
|-------|------------|---------|-------------|
| GET / | - | - | Page d'accueil (welcome) |
| GET /boutique | MarketplaceController | index() | Liste produits |
| GET /boutique/{product} | MarketplaceController | show() | Détail produit |

### 6.2 Routes Authentifiées

| Route | Middleware | Controller | Description |
|-------|------------|------------|-------------|
| GET /dashboard | auth, verified | - | Dashboard utilisateur |
| GET /profile | auth | UserController@show | Profil utilisateur |
| POST /profile/update | auth | UserController@update | Mise à jour profil |

### 6.3 Routes Wallet (Auth)

| Route | Controller | Méthode | Description |
|-------|------------|---------|-------------|
| GET /wallet/recharge | WalletController | showRecharge() | Formulaire recharge |
| POST /wallet/recharge | WalletController | processRecharge() | Traiter recharge |

### 6.4 Routes Cart & Checkout (Auth)

| Route | Controller | Méthode | Description |
|-------|------------|---------|-------------|
| GET /cart | CheckoutController | index() | Page panier |
| GET /cart/add/{productId} | CheckoutController | add() | Ajouter au panier |
| POST /cart/update | CheckoutController | update() | Modifier quantité |
| GET /cart/remove/{productId} | CheckoutController | remove() | Retirer produit |
| POST /checkout/process | CheckoutController | process() | Valider commande |

### 6.5 Routes Orders (Auth)

| Route | Controller | Méthode | Description |
|-------|------------|---------|-------------|
| GET /my-orders | OrderHistoryController | index() | Historique commandes |
| GET /my-orders/pending | OrderHistoryController | pending() | Commandes en cours |
| GET /my-orders/history/{order} | OrderHistoryController | show() | Détail commande |
| POST /my-orders/{order}/confirm | OrderHistoryController | confirmDelivery() | Confirmer livraison |
| GET /my-orders/{order}/receipt | OrderHistoryController | downloadReceipt() | Télécharger reçu |

### 6.6 Routes Seller (Auth)

| Route | Middleware | Controller | Méthode | Description |
|-------|------------|------------|---------|-------------|
| GET /seller/license | auth | SellerController | showLicenseForm() | Page licence |
| POST /seller/license/wallet | auth | SellerController | purchaseWithWallet() | Acheter licence |
| GET /seller/dashboard | auth, EnsureUserIsActiveSeller | ProductController | index() | Dashboard vendeur |
| Resource /seller/products | auth, EnsureUserIsActiveSeller | ProductController | - | CRUD produits |

### 6.7 Routes KYC

| Route | Middleware | Controller | Méthode | Description |
|-------|------------|------------|---------|-------------|
| GET /kyc | auth | KycController | showForm() | Formulaire KYC |
| POST /kyc | auth | KycController | store() | Soumettre KYC |

### 6.8 Routes Admin

| Route | Middleware | Controller | Méthode | Description |
|-------|------------|------------|---------|-------------|
| GET /admin/kyc | auth, can:admin-access | KycController (Admin) | index() | Liste demandes KYC |
| POST /admin/kyc/{user}/approve | auth, can:admin-access | KycController (Admin) | approve() | Approuver KYC |
| POST /admin/kyc/{user}/reject | auth, can:admin-access | KycController (Admin) | reject() | Rejeter KYC |

---

## 7. VUES & INTERFACE

### 7.1 Structure des Vues

```
resources/views/
├── layouts/
│   ├── layoutMaster.blade.php      # Layout principal Vuexy
│   └── ...
├── pages/
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   ├── marketplace/
│   │   ├── index.blade.php         # Liste produits
│   │   ├── show.blade.php          # Détail produit
│   │   ├── cart.blade.php          # Page panier
│   │   └── cart_modal_content.blade.php  # Modal panier
│   ├── seller/
│   │   ├── license.blade.php       # Page licence
│   │   ├── dashboard.blade.php     # Dashboard vendeur
│   │   ├── create.blade.php        # Créer produit
│   │   └── edit.blade.php          # Éditer produit
│   ├── orders/
│   │   ├── index.blade.php         # Historique
│   │   ├── pending.blade.php       # Commandes en cours
│   │   └── show.blade.php          # Détail commande
│   ├── wallet/
│   │   └── recharge.blade.php      # Recharge wallet
│   ├── kyc/
│   │   └── form.blade.php          # Formulaire KYC
│   └── admin/
│       └── kyc/
│           └── index.blade.php     # Admin KYC
├── pdf/
│   └── receipt.blade.php           # Template reçu PDF
├── dashboard.blade.php             # Dashboard utilisateur
└── welcome.blade.php               # Page d'accueil moderne
```

### 7.2 Composants Frontend

**Thème**: Vuexy (Bootstrap 4/5)
**Icons**: Boxicons
**Styles**: CSS personnalisés + Vuexy base
**JS**: Vanilla JS + jQuery (Vuexy)

---

## 8. BASE DE DONNÉES

### 8.1 Migrations Principales

| Fichier | Description |
|---------|-------------|
| `create_users_table.php` | Utilisateurs + KYC fields |
| `create_seller_products_tables.php` | Products, Categories, SellerProfiles, ProductImages |
| `create_cart_tables.php` | Carts, CartItems |
| (Implicites) Wallets, Transactions, Orders, OrderItems | Tables financières et commandes |

### 8.2 Relations Clés

```
User (1) ─────── (1) Wallet
  │
  ├── (1) ─────── (1) Cart ─────── (N) CartItems ─────── (1) Product
  │
  ├── (1) ─────── (1) SellerProfile
  │
  └── (1) ─────── (N) Orders ─────── (N) OrderItems ─────── (1) Product

Product (1) ─────── (N) ProductImages
Product (N) ─────── (1) Category
Product (N) ─────── (1) Seller (User)

Transaction (N) ─────── (1) Sender Wallet
Transaction (N) ─────── (1) Receiver Wallet
```

---

## 9. SÉCURITÉ & AUTHENTIFICATION

### 9.1 Authentification

**Package**: Laravel Fortify
**Features**:
- Registration
- Login
- Email Verification
- Password Reset
- Two-Factor Authentication (2FA)

### 9.2 Autorisations

**Gates & Policies**:
- `admin-access`: Gate pour routes admin

**Middleware**:
- `auth`: Authentification requise
- `verified`: Email vérifié
- `EnsureUserIsActiveSeller`: Vendeur actif et KYC vérifié

### 9.3 Sécurité des Données

**UUID**: Utilisation de UUIDs au lieu d'IDs auto-incrémentés (HasUuids trait)

**Mass Assignment Protection**: $fillable défini sur tous les modèles

**Password Hashing**: Automatique via Laravel

**CSRF Protection**: Token automatique sur tous les formulaires

---

## 10. FLUX DE DONNÉES CRITIQUES

###  10.1 Flux d'Achat Complet

```
1. User browse /boutique
   → MarketplaceController@index
   → Produits affichés

2. User clique "Ajouter au panier"
   → CheckoutController@add
   → CartService@add
   → CartItem créé

3. User va au panier (/cart)
   → CheckoutController@index
   → CartService@getGroupedBySeller
   → Panier groupé par vendeur affiché

4. User clique "Commander"
   → CheckoutController@process
   → OrderService@processCheckout {
      a. Vérifie solde wallet
      b. Débite wallet du montant total
      c. Créer Transaction
      d. Groupe items par vendeur
      e. Pour chaque vendeur:
         - Créer Order
         - Créer OrderItems
         - Décrémenter stock produits
   }
   → CartService@clear
   → Redirect /my-orders/pending

5. Livraison
   → Livreur livre produit
   → User reçoit delivery_code

6. User confirme livraison
   → OrderHistoryController@confirmDelivery
   → Valide delivery_code
   → delivery_status = 'delivered'
   → (Future) Paiement libéré au vendeur
```

### 10.2 Flux Vendeur

```
1. User veut devenir vendeur
   → /seller/license
   → SellerController@showLicenseForm

2. User achète licence (50 USD)
   → SellerController@purchaseWithWallet
   → PaymentService@processPayment (débite wallet)
   → SellerProfile créé (license_expires_at = +1 an)
   → Redirect /seller/dashboard

3. Seller crée produit
   → /seller/products/create
   → SellerEspaceBoutiqueController@create
   → Formulaire affiché

4. Seller soumet produit
   → SellerEspaceBoutiqueController@store
   → ProductService valide et crée
   → Product créé + Images uploadées
   → Redirect dashboard vendeur
```

---

## 11. SUGGESTIONS D'AMÉLIORATION

### 11.1 🔴 Critiques (Haute Priorité)

#### A. Système de Paiement au Vendeur
**Problème**: Actuellement, quand un achat est fait, le wallet de l'acheteur est débité mais le vendeur n'est JAMAIS payé.

**Solution Recommandée**:
```php
// Dans OrderHistoryController@confirmDelivery
if ($order->delivery_status !== 'delivered') {
    $order->update(['delivery_status' => 'delivered']);
    
    // +++++ AJOUTER CECI +++++
    // Calculer commission plateforme (ex: 5%)
    $platformFee = $order->total_amount * 0.05;
    $sellerAmount = $order->total_amount - $platformFee;
    
    // Créditer le vendeur
    $sellerId = $order->items->first()->product->seller_id;
    $sellerWallet = User::find($sellerId)->wallet;
    
    $sellerWallet->increment('balance', $sellerAmount);
    
    Transaction::create([
        'receiver_wallet_id' => $sellerWallet->id,
        'type' => 'seller_payment',
        'amount' => $sellerAmount,
        'reference' => 'SELL-' . $order->id,
        'description' => 'Paiement commande #' . $order->id,
        'status' => 'completed'
    ]);
}
```

**Impact**: ⭐⭐⭐⭐⭐ ESSENTIEL pour le bon fonctionnement fintech

---

#### B. Validation de Stock au Checkout
**Problème**: Le stock est vérifié à l'ajout au panier, mais pas au checkout. Un produit peut être vendu en rupture.

**Solution**:
```php
// Dans OrderService@processCheckout, avant de créer les orders
foreach ($items as $itemData) {
    $product = Product::find($itemData['id']);
    if (!$product || $product->stock_quantity < $itemData['quantity']) {
        throw new Exception("Stock insuffisant pour {$product->title}");
    }
}
```

**Impact**: ⭐⭐⭐⭐⭐ Critique pour éviter survente

---

#### C. Gestion des Erreurs de Paiement
**Problème**: Si le paiement échoue, le panier est quand même vidé.

**Solution**:
```php
// Dans CheckoutController@process
try {
    $orderService->processCheckout(...);
    $this->cartService->clear(); // ONLY if success
} catch (\Exception $e) {
    // DON'T clear cart - let user retry
    return back()->withErrors(['error' => $e->getMessage()]);
}
```

**Impact**: ⭐⭐⭐⭐ UX majeure

---

### 11.2 🟡 Importantes (Moyenne Priorité)

#### D. Notifications Email
**Manquant**: Aucune notification email actuellement

**À Implémenter**:
- Email de confirmation de commande
- Email de livraison au vendeur
- Email de confirmation de livraison
- Notification de vérification KYC

**Implémentation**:
```php
// Utiliser Laravel Notifications
php artisan make:notification OrderPlacedNotification

// Dans OrderService@processCheckout
$user->notify(new OrderPlacedNotification($order));
```

**Impact**: ⭐⭐⭐⭐ Professionnalisme

---

#### E. Validation de Formulaire Renforcée
**Recommandation**: Utiliser Form Requests pour centraliser la validation

**Exemple**:
```php
php artisan make:request StoreProductRequest

// Dans SellerEspaceBoutiqueController
public function store(StoreProductRequest $request) {
    // Validation automatique
}
```

**Impact**: ⭐⭐⭐ Maintenabilité

---

#### F. Logs & Monitoring
**Manquant**: Logging des transactions critiques

**Recommandation**:
```php
// Dans OrderService@processCheckout
Log::info('Checkout initiated', [
    'user_id' => $user->id,
    'total' => $totalAmount,
    'items_count' => count($cartItems)
]);

// En cas d'erreur
Log::error('Checkout failed', [
    'user_id' => $user->id,
    'error' => $e->getMessage()
]);
```

**Impact**: ⭐⭐⭐⭐ Debugging & Audit

---

### 11.3 🟢 Améliorations (Basse Priorité)

#### G. Cache des Produits
**Optimisation**: Cache la liste des produits populaires

```php
$products = Cache::remember('featured_products', 3600, function () {
    return Product::where('stock_quantity', '>', 0)
        ->latest()
        ->take(12)
        ->get();
});
```

**Impact**: ⭐⭐⭐ Performance

---

#### H. Queue pour les Emails
**Optimisation**: Envoyer les emails en arrière-plan

```php
// Dans config/queue.php
'default' => env('QUEUE_CONNECTION', 'database'),

// Utiliser
$user->notify(new OrderPlacedNotification($order));

// Devient asynchrone automatiquement
```

**Impact**: ⭐⭐⭐ Performance

---

#### I. Soft Deletes
**Recommandation**: Utiliser SoftDeletes sur Products et Orders

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use SoftDeletes;
}
```

**Impact**: ⭐⭐ Data Safety

---

#### J. API REST
**Extension**: Créer une API RESTful pour mobile app future

```php
// routes/api.php
Route::apiResource('products', ProductApiController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartApiController::class, 'add']);
});
```

**Impact**: ⭐⭐⭐⭐ Scalabilité

---

### 11.4 💡 Idées Innovantes

#### K. Programme de Fidélité
**Concept**: Points de fidélité gagnés à chaque achat

**Implémentation**:
```php
// Migration
Schema::create('loyalty_points', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id');
    $table->integer('points')->default(0);
    $table->timestamps();
});

// Lors d'achat confirmé
$user->loyaltyPoints()->increment('points', floor($order->total_amount));

// Utilisation
if ($request->use_points && $user->loyaltyPoints->points >= 1000) {
    $discount = 10; // 1000 points = 10 USD
    $totalAmount -= $discount;
    $user->loyaltyPoints()->decrement('points', 1000);
}
```

**Impact**: ⭐⭐⭐⭐ Rétention utilisateurs

---

#### L. Système de Reviews & Ratings
**Concept**: Avis et notes sur produits et vendeurs

**Implémentation**:
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id');
    $table->foreignUuid('product_id');
    $table->foreignUuid('order_id'); // Only if purchased
    $table->integer('rating'); // 1-5
    $table->text('comment')->nullable();
    $table->timestamps();
});

// Affichage moyenne
$product->averageRating = $product->reviews()->avg('rating');
```

**Impact**: ⭐⭐⭐⭐⭐ Confiance plateforme

---

#### M. Chat en Temps Réel (Buyer-Seller)
**Concept**: Communication directe acheteur/vendeur

**Technologies**:
- Laravel Websockets / Pusher
- Vue.js pour le chat UI

**Impact**: ⭐⭐⭐⭐ Expérience utilisateur

---

#### N. Système de Parrainage
**Concept**: Gagner des bonus en invitant des amis

```php
// User invite friend
$referralCode = Str::random(8);
$user->update(['referral_code' => $referralCode]);

// Friend registers with code
if ($request->referral_code) {
    $referrer = User::where('referral_code', $request->referral_code)->first();
    $referrer->wallet->increment('balance', 5); // 5 USD bonus
    $user->wallet->increment('balance', 5); // Welcome bonus
}
```

**Impact**: ⭐⭐⭐⭐ Croissance virale

---

#### O. Multi-Devises
**Concept**: Support USD, CDF,EUR

**Implémentation**:
```php
// Service de conversion
class CurrencyService {
    public function convert($amount, $from, $to) {
        $rates = [
            'USD' => 1,
            'CDF' => 2500,
            'EUR' => 0.92
        ];
        return ($amount / $rates[$from]) * $rates[$to];
    }
}

// Affichage
$price_usd = $product->price;
$price_cdf = CurrencyService::convert($price_usd, 'USD', 'CDF');
```

**Impact**: ⭐⭐⭐⭐ Marché local

---

#### P. Dashboard Analytique Vendeur
**Concept**: Stats détaillées pour vendeurs

**Métriques**:
- Ventes par période
- Produits les plus vendus
- Revenus totaux
- Taux de conversion

**Libraries**: Chart.js, ApexCharts

**Impact**: ⭐⭐⭐⭐ Autonomie vendeurs

---

#### Q. Mobile App (React Native / Flutter)
**Concept**: Application mobile native

**Features**:
- Notifications push
- Scan QR codes
- Géolocalisation pour livraison
- Paiement mobile money intégré

**Impact**: ⭐⭐⭐⭐⭐ Expansion marché

---

## 12. OPTIMISATIONS TECHNIQUES

### 12.1 Performance Database

#### Eager Loading
**Actuel**: Bon usage de `with()` dans plusieurs endroits

**À Améliorer**:
```php
// Dans MarketplaceController@index
// Actuel
$products = Product::with(['category', 'images', 'seller'])->get();

// Optimisé
$products = Product::with([
    'category:id,name', // Select only needed fields
    'images' => function($q) {
        $q->where('is_primary', true)->limit(1); // Only thumbnail
    },
    'seller:id,name'
])->get();
```

---

#### Indexation Database
**Recommandation**: Ajouter des index sur les colonnes fréquemment interrogées

```php
// Migration
$table->index('seller_id');
$table->index('category_id');
$table->index('stock_quantity');
$table->index(['status', 'created_at']); // Composite index
```

---

### 12.2 Sécurité

#### Rate Limiting
**Recommandation**: Limiter les tentatives de login et d'API calls

```php
// routes/web.php
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

---

#### Input Sanitization
**Amélioration**: Sanitizer les inputs utilisateur

```php
// Helper
function sanitize($input) {
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

// Usage dans controllers
$product->title = sanitize($request->title);
```

---

### 12.3 Code Quality

#### Service Provider pour Services
**Recommandation**: Enregistrer les services dans un ServiceProvider

```php
// app/Providers/AppServiceProvider.php
public function register()
{
    $this->app->singleton(CartService::class);
    $this->app->singleton(OrderService::class);
    // ...
}
```

---

#### Repository Pattern
**Amélioration Future**: Abstraire les requêtes database

```php
// app/Repositories/ProductRepository.php
class ProductRepository {
    public function getAllAvailable() {
        return Product::where('stock_quantity', '>', 0)->get();
    }
    
    public function findWithDetails($id) {
        return Product::with(['images', 'category', 'seller'])->find($id);
    }
}
```

**Impact**: ⭐⭐⭐ Testabilité & Maintenabilité

---

## 13. TESTS

### 13.1 État Actuel
**Observation**: Pas de tests détectés dans le rapport

### 13.2 Recommandations

#### Tests Feature
```php
// tests/Feature/CheckoutTest.php
public function test_user_can_checkout_with_wallet()
{
    $user = User::factory()->create();
    $user->wallet()->create(['balance' => 100]);
    
    $product = Product::factory()->create(['price' => 50]);
    
    $this->actingAs($user)
        ->post('/checkout/process', ['type' => 'wallet'])
        ->assertRedirect('/my-orders/pending');
        
    $this->assertEquals(50, $user->fresh()->wallet->balance);
}
```

#### Tests Unit
```php
// tests/Unit/CartServiceTest.php
public function test_add_product_to_cart()
{
    $service = new CartService();
    $product = Product::factory()->create();
    
    $count = $service->add($product, 2);
    
    $this->assertEquals(1, $count);
}
```

**Impact**: ⭐⭐⭐⭐⭐ Qualité & Confiance

---

## 14. DOCUMENTATION DÉVELOPPEUR

### 14.1 README.md
**Recommandation**: Créer/Enrichir le README avec:
- Installation steps
- Configuration .env
- Database setup
- Seeder commands
- Common tasks

### 14.2 API Documentation
**Si API future**: Utiliser Scribe ou API Platform

```bash
composer require knuckleswtf/scribe
php artisan scribe:generate
```

---

## 15. CONCLUSION

### 15.1 Points Forts 💪

1. **Architecture Solide**: MVC + Service Layer bien implémenté
2. **Séparation des Responsabilités**: Controllers légers, logique dans Services
3. **Sécurité**: UUID, Fortify, Middleware personnalisés
4. **Hybrid E-commerce/Fintech**: Concept innovant bien exécuté
5. **Multi-Seller Support**: Gestion automatique de plusieurs vendeurs
6. **Wallet-First**: Approche fintech cohérente
7. **KYC Integration**: Conformité et sécurité

### 15.2 Points à Améliorer ⚠️

1. **Paiement Vendeur**: CRITIQUE - Vendeurs jamais payés actuellement
2. **Validation Stock**: Vérifier stock au checkout, pas seulement à l'ajout panier
3. **Notifications**: Aucun email envoyé
4. **Tests**: Absence de tests automatisés
5. **Logs**: Manque de logging des transactions critiques
6. **Error Handling**: Certains cas d'erreur mal gérés

### 15.3 Prochaines Étapes Recommandées 🚀

**Phase 1 - Corrections Critiques (1-2 semaines)**
1. Implémenter paiement aux vendeurs
2. Ajouter validation stock au checkout
3. Fix error handling dans checkout
4. Ajouter logs transactions

**Phase 2 - Améliorations Core (2-4 semaines)**
5. Système de notifications email
6. Tests unitaires et feature
7. Dashboard analytique vendeur
8. Système de reviews

**Phase 3 - Features Innovantes (1-2 mois)**
9. Programme de fidélité
10. Chat acheteur-vendeur
11. Multi-devises
12. API REST pour mobile

**Phase 4 - Scalabilité (2-3 mois)**
13. Mobile app (React Native/Flutter)
14. Queue workers pour emails
15. Cache optimization
16. CDN pour images

---

## 16. ANNEXES

### A. Commandes Utiles

```bash
# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Queue
php artisan queue:work

# Storage
php artisan storage:link

# Tests
php artisan test
```

### B. Variables d'Environnement Clés

```env
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=jouan_sugu
WALLET_CURRENCY=USD
LICENSE_PRICE=50
LICENSE_DURATION_MONTHS=12
```

### C. Ressources Externes

- Laravel Documentation: https://laravel.com/docs
- Fortify: https://laravel.com/docs/fortify
- Vuexy Theme: Documentation interne
- Boxicons: https://boxicons.com

---

**FIN DU RAPPORT**

Ce rapport a été généré automatiquement par analyse du code source le 2 Février 2026.

Pour toute question ou clarification, consulter `.agent/PROJECT_CONTEXT.md` ou contacter l'équipe technique.

---

## 21. SYSTÈME D'INTERNATIONALISATION (I18N) - Février 2026

### 21.1 Vue d'Ensemble

**Langues Support ées** : Français (fr), Anglais (en)
**Fichiers de Traduction** : `lang/en.json`, `lang/fr.json`
**Total de Clés** : 80+ chaînes traduites

### 21.2 Architecture

```
┌─────────────────┐
│  Session locale │ ← Stocke la langue choisie
└─────────────────┘
        ↓
┌─────────────────┐
│ SetLocale      │ ← Middleware (bootstrap/app.php)
│ Middleware      │
└─────────────────┘
        ↓
┌─────────────────┐
│  App::setLocale │ ← Applique la langue
└─────────────────┘
        ↓
┌─────────────────┐
│  __() Helper    │ ← Utilisépartout dans le code
└─────────────────┘
```

### 21.3 Middleware SetLocale

**Fichier** : `app/Http/Middleware/SetLocale.php`

```php
public function handle(Request $request, Closure $next): Response
{
    if (session()->has('locale')) {
        App::setLocale(session()->get('locale'));
    }
    return $next($request);
}
```

**Enregistrement** : `bootstrap/app.php` (middleware global)

### 21.4 Contrôleur de Langue

**Fichier** : `app/Http/Controllers/langue/LanguageController.php`

**Route** : `GET /lang/{locale}`

**Méthode** :
```php
public function swap($locale)
{
    if (! in_array($locale, ['en', 'fr'])) {
        abort(400);
    }
    session()->put('locale', $locale);
    return redirect()->back();
}
```

### 21.5 Fichiers de Traduction

**Format JSON** : Clé-valeur simple

**Exemples de Clés** :
-  Gestion: Dashboard, Profile Management, Configuration
- Actions: Create, Update, Delete, Save, Cancel
- Messages: Profile created successfully., Access denied...
- Navigation: Boutique, Mes Achats, Espace Vendeur

### 21.6 Utilisation dans le Code

#### Dans les Views Blade
```blade
{{-- Titre de page --}}
@section('title', __('Profile Management'))

{{-- Texte affiché --}}
<h1>{{ __('Dashboard') }}</h1>

{{-- Attributs HTML --}}
<button title="{{ __('Click here') }}">...</button>

{{-- JavaScript --}}
<script>
    title: '{{ __('Are you sure?') }}',
    confirmButtonText: '{{ __('Yes, delete!') }}'
</script>
```

#### Dans les Contrôleurs
```php
return redirect()->route('admin.profils.index')
    ->with('success', __('Profile created successfully.'));

abort(403, __('Access denied. Only Super-Admins can access this section.'));
```

#### Dans le Menu
**Fichier** : `resources/views/layouts/sections/menu/verticalMenu.blade.php`
```blade
<div>{{ __($menu->name) }}</div>
```

Le menu JSON utilise des clés qui sont automatiquement traduites.

### 21.7 Sélecteur de Langue UI

**Emplacement** : `resources/views/layouts/sections/navbar/navbar.blade.php` (lignes 52-71)

**Composants** :
- Dropdown dans la navbar
- Icônes drapeaux : `fi fi-fr` (🇫🇷), `fi fi-us` (🇺🇸)
- Détection automatique de la langue active

**Code** :
```blade
<li class="nav-item dropdown-language dropdown">
    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
        <i class='fi fi-{{ app()->getLocale() == 'fr' ? 'fr' : 'us' }} fis'></i>
    </a>
    <ul class="dropdown-menu">
        <li><a href="{{ url('lang/en') }}">English</a></li>
        <li><a href="{{ url('lang/fr') }}">French</a></li>
    </ul>
</li>
```

### 21.8 DataTables Localisation

**Fichier français** : `public/assets/vendor/libs/datatables-bs5/i18n/fr-FR.json`

**Utilisation** :
```javascript
$('#profilsTable').DataTable({
    language: {
        url: '{{ asset("assets/vendor/libs/datatables-bs5/i18n/fr-FR.json") }}'
    }
});
```

**Important** : Utiliser des fichiers LOCAUX, pas de CDN.

### 21.9 Bonnes Pratiques

1. **Clés descriptives** : `__('Profile created successfully.')` plutôt que `__('msg_001')`
2. **Cohérence** : Les deux fichiers JSON doivent avoir exactement les mêmes clés
3. **Texte en anglais comme clé** : Plus lisible dans le code
4. **Pas de mixage** : Soit tout traduit, soit rien (éviter les chaînes mixtes)

---

## 22. SYSTÈME DE PROFILS & PERMISSIONS - Février 2026

### 22.1 Architecture Profils

Remplacement du champ `role` par un système de profils granulaires.

#### 22.1.1 Table `profils`

**Champs** :
- `id` (UUID, PK)
- `libelle` (string, unique) - Ex: "Super-Admin", "utilisateur"
- `timestamps`

**Profils Par Défaut** :
1. Super-Admin
2. utilisateur
3. Agent de support
4. Verificateur kyc
5. Admin-entreprise

#### 22.1.2 Table `roles`

**Champs** :
- `id` (UUID, PK)
- `nom` (string)
- `slug` (string, unique)
- `description` (text nullable)
- `timestamps`

**Objectif** : Rôles granulaires pour permissions avancées futures

#### 22.1.3 Table `role_details` (Pivot)

**Champs** :
- `id` (UUID, PK)
- `user_id` (FK → users)
- `role_id` (FK → roles)
- `timestamps`

**Contrainte** : UNIQUE(user_id, role_id)

### 22.2 Modèles

#### Profil Model
```php
class Profil extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = ['libelle'];
    
    public function users()
    {
        return $this->hasMany(User::class, 'profil_id');
    }
}
```

#### User Model (Relations)
```php
public function profil()
{
    return $this->belongsTo(Profil::class, 'profil_id');
}

public function roles()
{
    return $this->belongsToMany(Role::class, 'role_details', 'user_id', 'role_id');
}

public function isSuperAdmin(): bool
{
    return $this->profil?->libelle === 'Super-Admin';
}

public function hasRole(string $roleSlug): bool
{
    return $this->roles()->where('slug', $roleSlug)->exists();
}
```

### 22.3 Gates Laravel

**Fichier** : `app/Providers/AppServiceProvider.php`

```php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('admin-access', function (User $user) {
        return $user->isSuperAdmin();
    });
    
    Gate::define('super-admin-access', function (User $user) {
        return $user->isSuperAdmin();
    });
}
```

### 22.4 Utilisation dans les Routes

```php
// Routes Admin (tous les admins)
Route::middleware('can:admin-access')->prefix('admin')->group(function () {
    Route::get('/kyc', [KycController::class, 'index']);
});

// Routes Super-Admin UNIQUEMENT
Route::middleware('can:super-admin-access')->prefix('admin')->group(function () {
    Route::resource('profils', ProfilController::class);
});
```

### 22.5 Contrôleur de Gestion des Profils

**Fichier** : `app/Http/Controllers/Admin/ProfilController.php`

**Routes** : `/admin/profils` (resource)

**Méthodes** :
- `index()` : Liste des profils avec compteur utilisateurs
- `create()` : Formulaire création
- `store(Request)` : Créer nouveau profil
- `edit($id)` : Formulaire édition
- `update(Request, $id)` : Mettre à jour profil
- `destroy($id)` : Supprimer profil (si aucun utilisateur ne l'utilise)

**Protection** : Middleware `can:super-admin-access`

**Validation** :
- `libelle` : required, string, max:255, unique

**Logique Suppression** :
```php
if ($profil->users()->count() > 0) {
    return redirect()->back()
        ->with('error', __('Cannot delete this profile because users are using it.'));
}
```

### 22.6 Menu Vertical - Flag superAdminOnly

**Fichier** : `resources/menu/verticalMenu.json`

```json
{
    "name": "Configuration",
    "icon": "ti ti-settings",
    "adminOnly": true,
    "superAdminOnly": true,
    "submenu": [
        {
            "url": "admin/profils",
            "name": "Gestion des Profils"
        }
    ]
}
```

**Template** : `resources/views/layouts/sections/menu/verticalMenu.blade.php`
```blade
@if(isset($menu->superAdminOnly) && $menu->superAdminOnly)
    @if(!auth()->user()->isSuperAdmin())
        @continue
    @endif
@endif
```

### 22.7 Migrations

**Exécutées** :
1. `2026_02_03_100001_create_profils_table.php`
2. `2026_02_03_100002_create_roles_table.php`
3. `2026_02_03_100003_create_role_details_table.php`
4. `2026_02_03_100004_add_profil_id_to_users_table.php`
5. `2026_02_03_100005_migrate_existing_roles_to_profils.php` (migration de données)
6. `2026_02_03_100006_remove_role_from_users_table.php`

### 22.8 Seeders

**ProfilSeeder** : Populate par défaut les 5 profils

**AdminUserSeeder** : Modifié pour utiliser `profil_id` au lieu de `role`

### 22.9 Bonnes Pratiques

1. **Gates > Vérifications Manuelles** : Toujours utiliser `can:gate-name` au lieu de méthodes dans les contrôleurs
2. **Middleware sur Routes** : Protéger au niveau des routes, pas dans les contrôleurs
3. **Profils Uniques** : Le libellé est unique pour identifier clairement chaque profil
4. **Pas de Suppression si Utilisé** : Empêcher la suppression d'un profil actif

---

## 23. RÉCAPITULATIF DES CHANGEMENTS MAJEURS 2026

### Février 2026

| Date | Module | Changements |
|------|--------|-------------|
| 2026-02-03 | **i18n** | Système de traduction FR/EN complet (80+ clés) |
| 2026-02-03 | **i18n** | SetLocale middleware activé |
| 2026-02-03 | **i18n** | Sélecteur de langue dans navbar |
| 2026-02-03 | **i18n** | DataTables localisé (fichier local fr-FR.json) |
| 2026-02-03 | **Permissions** | Système de profils (remplacement du champ `role`) |
| 2026-02-03 | **Permissions** | Gates Laravel (`admin-access`, `super-admin-access`) |
| 2026-02-03 | **Admin** | ProfilController pour gestion des profils |
| 2026-02-03 | **Admin** | Menu Configuration (superAdminOnly flag) |

---

**Rapport mis à jour** : 2026-02-03  
**Version** : 1.2  
**Sections ajoutées** : 21 (i18n), 22 (Profils & Permissions), 23 (Récapitulatif)
