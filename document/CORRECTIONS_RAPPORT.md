# Corrections à Apporter au Rapport Technique

## Problème Identifié
Le rapport technique contient des chemins obsolètes qui datent **d'AVANT** le refactoring domain-based effectué le 2026-02-02.

## ❌ Anciennes Localisations (OBSOLÈTES)

### Controllers
- `app/Http/Controllers/MarketplaceController.php`
- `app/Http/Controllers/CheckoutController.php`
- `app/Http/Controllers/OrderHistoryController.php`
- `app/Http/Controllers/user/KycController.php`
- `app/Http/Controllers/admin/AdminKycController.php`
- `app/Http/Controllers/seller/SellerController.php`
- `app/Http/Controllers/seller/SellerEspaceBoutiqueController.php`
- `app/Http/Controllers/wallet/WalletController.php`

### Services
- `app/Services/CartService.php`
- `app/Services/OrderService.php`
- `app/Services/PaymentService.php`
- `app/Services/ProductService.php`
- `app/Services/KycService.php`
- `app/Services/LicenseService.php`
- `app/Services/MobileMoneyService.php`

## ✅ Nouvelles Localisations (ACTUELLES)

### Controllers Web
- `app/Http/Controllers/Web/Marketplace/MarketplaceController.php`
- `app/Http/Controllers/Web/Cart/CheckoutController.php`
- `app/Http/Controllers/Web/Order/OrderHistoryController.php`
- `app/Http/Controllers/Web/User/UserController.php`
- `app/Http/Controllers/Web/User/KycController.php`
- `app/Http/Controllers/Web/Seller/SellerController.php`
- `app/Http/Controllers/Web/Seller/ProductController.php` (ex-SellerEspaceBoutiqueController)
- `app/Http/Controllers/Web/Wallet/WalletController.php`
- `app/Http/Controllers/Web/Admin/KycController.php` (ex-AdminKycController)

### Controllers API
- `app/Http/Controllers/Api/Auth/AuthApiController.php`
- `app/Http/Controllers/Api/Wallet/WalletApiController.php`
- `app/Http/Controllers/Api/Seller/SellerApiController.php`

### Services
- `app/Services/Marketplace/ProductService.php`
- `app/Services/Cart/CartService.php`
- `app/Services/Order/OrderService.php`
- `app/Services/Payment/PaymentService.php`
- `app/Services/Payment/MobileMoneyService.php`
- `app/Services/User/KycService.php`
- `app/Services/Seller/LicenseService.php`

## Lignes à Corriger dans RAPPORT_TECHNIQUE_2026.md

1. Ligne 159 : MarketplaceController
2. Ligne 206 : CheckoutController
3. Ligne 207-208 : CartService, OrderService
4. Ligne 287 : WalletController
5. Ligne 288-289 : PaymentService, MobileMoneyService
6. Ligne 347-348 : KycController, AdminKycController
7. Ligne 349 : KycService
8. Ligne 416-419 : Seller controllers et services
9. Ligne 501 : OrderHistoryController
10. Lignes 686-692 : Tableau récapitulatif des services

## Nom de Classe Incorrect

- `SellerEspaceBoutiqueController` → **`ProductController`** (ligne 417)
- `AdminKycController` → **`KycController`** (Web/Admin namespace différencie)
