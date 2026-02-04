# Audit des Pages - Protection par Features

Ce document liste toutes les pages de l'application et les protections à ajouter.

---

## 📊 Résumé

**Total Pages** : 35  
**Pages Protégées** : ~10  
**Pages à Protéger** : ~25

---

## ✅ Pages Déjà Protégées

### Admin - KYC Management
**Fichier** : `admin/kyc-management.blade.php`  
**Status** : ✅ Partiellement protégé

**Protections existantes** :
- `<x-feature-button feature="admin.kyc.approve">` - Bouton approuver
- `<x-feature-button feature="admin.kyc.reject">` - Bouton rejeter

**À ajouter** :
- [ ] Section liste KYC pending : `<x-feature-section feature="admin.kyc.view-pending">`
- [ ] Section historique KYC : `<x-feature-section feature="admin.kyc.view-all">`

### Seller - Espace Boutique
**Fichier** : `seller/espace_boutique.blade.php`  
**Status** : ✅ Protégé

**Protections existantes** :
- `<x-feature-link feature="seller.products.create">` - Bouton ajouter produit
- `<x-feature-link feature="seller.products.edit">` - Lien éditer
- `<x-feature-button feature="seller.products.delete">` - Bouton supprimer

---

## 🔴 Pages à Protéger (Priorité Haute)

### 1. User Profile
**Fichier** : `user/profile.blade.php`  
**Actions sensibles détectées** :
- Désactiver compte (ligne 170)

**Protections à ajouter** :
```blade
{{-- Section informations compte --}}
<x-feature-section feature="profile.view-account-info">
    <!-- Infos compte -->
</x-feature-section>

{{-- Bouton désactiver compte --}}
<x-feature-button feature="profile.deactivate-account" type="submit" class="btn btn-danger">
    Désactiver le compte
</x-feature-button>

{{-- Section changer mot de passe --}}
<x-feature-section feature="profile.change-password">
    <!-- Formulaire mot de passe -->
</x-feature-section>
```

---

### 2. Seller - Product Form
**Fichier** : `seller/product_form.blade.php`  
**Actions sensibles** :
- Ajouter/éditer produits
- Upload images
- Supprimer images

**Protections à ajouter** :
```blade
{{-- Bouton supprimer image --}}
<x-feature-button feature="seller.products.delete-image" type="button" class="btn btn-danger">
    <i class="bx bx-x"></i>
</x-feature-button>

{{-- Section upload images --}}
<x-feature-section feature="seller.products.upload-images">
    <!-- Formulaire upload -->
</x-feature-section>

{{-- Section prix et stock --}}
<x-feature-section feature="seller.products.manage-pricing">
    <!-- Prix, stock, etc -->
</x-feature-section>
```

---

### 3. User - KYC
**Fichier** : `user/kyc.blade.php`  
**Actions sensibles** :
- Soumettre KYC
- Upload documents

**Protections à ajouter** :
```blade
{{-- Section formulaire KYC --}}
<x-feature-section feature="user.kyc.submit">
    <!-- Formulaire KYC complet -->
</x-feature-section>
```

---

### 4. Wallet - Recharge
**Fichier** : `wallet/recharge.blade.php`  
**Actions sensibles** :
- Recharger wallet (financier)

**Protections à ajouter** :
```blade
{{-- Section recharge --}}
<x-feature-section feature="wallet.recharge" showDeniedMessage="true">
    <!-- Formulaire recharge -->
</x-feature-section>

{{-- Bouton soumettre --}}
<x-feature-button feature="wallet.recharge" type="submit" class="btn btn-primary">
    Recharger
</x-feature-button>
```

---

### 5. Admin - Profils (Roles)
**Fichier** : `admin/profils/index.blade.php`  
**Actions détectées** :
- Voir liste profils
- Créer/éditer/supprimer

**Protections à ajouter** :
```blade
{{-- Bouton créer --}}
<x-feature-link feature="admin.profils.create" route="{{ route('admin.profils.create') }}" class="btn btn-primary">
    Add New Profile
</x-feature-link>

{{-- Liens actions --}}
<x-feature-link feature="admin.profils.edit" route="..." class="dropdown-item">
    Edit
</x-feature-link>

<x-feature-button feature="admin.profils.delete" type="button" class="dropdown-item text-danger delete-btn">
    Delete
</x-feature-button>
```

---

### 6. Admin - Modules
**Fichier** : `admin/modules/index.blade.php`  
**Actions** :
- CRUD modules

**Protections à ajouter** :
```blade
<x-feature-link feature="admin.modules.create" route="{{ route('admin.modules.create') }}" class="btn btn-primary">
    Add Module
</x-feature-link>

<x-feature-link feature="admin.modules.edit" route="..." class="dropdown-item">
    Edit
</x-feature-link>

<x-feature-button feature="admin.modules.delete" type="button" class="dropdown-item text-danger">
    Delete
</x-feature-button>
```

---

### 7. Admin - Features
**Fichier** : `admin/features/index.blade.php`  
**Actions** :
- CRUD features

**Protections à ajouter** :
```blade
<x-feature-link feature="admin.features.create" route="{{ route('admin.features.create') }}" class="btn btn-primary">
    Add Feature
</x-feature-link>

<x-feature-button feature="admin.features.delete" type="button" class="dropdown-item text-danger">
    Delete
</x-feature-button>
```

---

### 8. Admin - Permissions
**Fichier** : `admin/permissions/index.blade.php`  
**Actions** :
- CRUD permissions

**Protections à ajouter** :
```blade
<x-feature-link feature="admin.permissions.create" route="{{ route('admin.permissions.create') }}" class="btn btn-primary">
    Add Permission
</x-feature-link>

<x-feature-link feature="admin.permissions.edit" route="..." class="dropdown-item">
    Edit
</x-feature-link>

<x-feature-button feature="admin.permissions.delete" type="button" class="dropdown-item text-danger">
    Delete
</x-feature-button>
```

---

### 9. User - Orders
**Fichier** : `user/orders/index.blade.php`, `user/orders/show.blade.php`

**Protections à ajouter** :
```blade
{{-- Section commandes --}}
<x-feature-section feature="user.orders.view">
    <!-- Liste commandes -->
</x-feature-section>

{{-- Bouton confirmer livraison --}}
<x-feature-button feature="user.orders.confirm-delivery" type="submit" class="btn btn-success">
    Confirmer Livraison
</x-feature-button>

{{-- Bouton télécharger reçu --}}
<x-feature-link feature="user.orders.download-receipt" route="..." class="btn btn-outline-primary">
    <i class="bx bx-download"></i> Télécharger Reçu
</x-feature-link>
```

---

## 🟡 Pages à Protéger (Priorité Moyenne)

### 10. Seller - Product Show
**Fichier** : `seller/product_show.blade.php`  
**Protections à ajouter** :
```blade
<x-feature-section feature="seller.products.view-details">
    <!-- Détails produit complet -->
</x-feature-section>

<x-feature-link feature="seller.products.edit" route="..." class="btn btn-primary">
    Edit Product
</x-feature-link>
```

---

### 11. Seller - License
**Fichier** : `seller/license.blade.php`  
**Protections à ajouter** :
```blade
<x-feature-section feature="seller.license.submit">
    <!-- Formulaire licence vendeur -->
</x-feature-section>

<x-feature-button feature="seller.license.submit" type="submit" class="btn btn-primary">
    Soumettre Demande
</x-feature-button>
```

---

### 12. Marketplace - Cart
**Fichier** : `marketplace/cart.blade.php`  
**Actions** :
- Modifier quantités
- Supprimer items
- Checkout

**Protections à ajouter** :
```blade
{{-- Section panier --}}
<x-feature-section feature="marketplace.cart.view">
    <!-- Contenu panier -->
</x-feature-section>

{{-- Bouton checkout --}}
<x-feature-button feature="marketplace.checkout.process" type="submit" class="btn btn-primary">
    Procéder au Paiement
</x-feature-button>
```

---

## 🟢 Pages Publiques (Pas de Protection Nécessaire)

### Auth Pages
- `auth/login.blade.php` - ✅ Public
- `auth/register.blade.php` - ✅ Public
- `auth/forgot-password.blade.php` - ✅ Public
- `auth/reset-password.blade.php` - ✅ Public
- `auth/verify-email.blade.php` - ✅ Public
- `auth/two-factor-challenge.blade.php` - ✅ Public

### Marketplace Public
- `marketplace/index.blade.php` - ✅ Public (navigation)
- `marketplace/show.blade.php` - ✅ Public (voir produit)

---

## 📋 Plan d'Action

### Phase 1 : Pages Admin (Priorité 1)
- [ ] `admin/profils/index.blade.php`
- [ ] `admin/modules/index.blade.php`
- [ ] `admin/features/index.blade.php`
- [ ] `admin/permissions/index.blade.php`
- [ ] `admin/kyc-management.blade.php` (compléter)

### Phase 2 : Pages Seller (Priorité 2)
- [ ] `seller/product_form.blade.php`
- [ ] `seller/product_show.blade.php`
- [ ] `seller/license.blade.php`

### Phase 3 : Pages User (Priorité 3)
- [ ] `user/profile.blade.php`
- [ ] `user/kyc.blade.php`
- [ ] `user/orders/index.blade.php`
- [ ] `user/orders/show.blade.php`
- [ ] `wallet/recharge.blade.php`

### Phase 4 : Pages Marketplace (Priorité 4)
- [ ] `marketplace/cart.blade.php`

---

## 🎯 Après Modifications

Pour chaque page modifiée :

1. **Scanner** : `php artisan features:scan`
2. **Vérifier** : `/admin/features` → Nouvelles features détectées
3. **Tester** : Se connecter sans permissions, vérifier accès refusé
4. **Documenter** : Cocher dans cette liste

---

## 📊 Progression

- **Total pages** : 35
- **Protégées** : 10 (29%)
- **À protéger** : 25 (71%)
- **Publiques** : 8 (23%)

**Objectif** : 100% des pages sensibles protégées avant production
