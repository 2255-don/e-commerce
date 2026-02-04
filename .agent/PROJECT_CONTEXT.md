# Contexte Projet Jouan-Sugu

> **IMPORTANT**: Ce document contient le contexte essentiel du projet. Tous les agents doivent consulter ce fichier au début de chaque conversation pour comprendre la nature et les spécificités du projet.

---

## Vue d'Ensemble du Projet

**Nom**: Jouan-Sugu  
**Type**: **Plateforme E-commerce & Fintech hybride**  
**Framework**: Laravel (PHP)  
**Localisation**: Bamako, Mali  
**Langue principale**: Français

### Nature Hybride du Projet

Jouan-Sugu n'est **PAS** seulement une plateforme e-commerce classique. C'est une **plateforme hybride** combinant :

1. **E-commerce** : Marketplace pour acheter et vendre des produits
2. **Fintech** : Services financiers intégrés (portefeuille digital, paiements, transactions)

### Fonctionnalités Principales

#### Côté E-commerce
- Marketplace de produits
- Système de boutiques pour vendeurs
- Gestion de panier et commandes
- Suivi de livraison
- Système de licence vendeur

#### Côté Fintech
- **Portefeuille Digital** (`wallet`) : Gestion de fonds pour les utilisateurs
- **Système KYC** : Vérification d'identité obligatoire
- **Paiements intégrés** : Les transactions passent par le wallet
- **Recharge de wallet** : Système de rechargement de compte
- **Transactions sécurisées** : Tous les paiements sont tracés et sécurisés

---

## Architecture & Technologies

### Stack Technique
- **Backend**: Laravel 10+
- **Frontend**: Blade Templates + Bootstrap 4/5
- **Thème Admin**: Vuexy
- **Icons**: Boxicons
- **Base de données**: MySQL

### Structure des Routes Principales

```php
// Public
GET  /                              -> Page d'accueil (welcome)
GET  /boutique                      -> Marketplace (navigation libre)
GET  /boutique/{product}            -> Détail produit

// Auth Required
GET  /dashboard                     -> Dashboard utilisateur
GET  /profile                       -> Profil utilisateur
GET  /cart                          -> Panier
GET  /wallet/recharge               -> Recharge wallet
GET  /my-orders                     -> Historique commandes
GET  /kyc                           -> Formulaire KYC

// Seller (Auth + Active Seller)
GET  /seller/license                -> Acquisition licence vendeur
GET  /seller/dashboard              -> Dashboard vendeur
Resource /seller/products           -> CRUD produits

// Admin
GET  /admin/kyc                     -> Gestion KYC
```

---

## Architecture API & Service Layer

> [!IMPORTANT]
> **RÈGLE OBLIGATOIRE** : Pour TOUTE nouvelle route web, créer également une route API correspondante.

### Pattern Service Layer (Anti-Duplication)

**Problème** : Si on crée un `WebController` et un `ApiController`, on risque de dupliquer toute la logique.

**Solution** : Utiliser un **Service Layer** pour centraliser la logique métier.

```
┌─────────────────┐
│    Service      │ ← Logique métier pure (réutilisable)
│  (CartService)  │
└─────────────────┘
         ↓
    ┌────┴─────┐
    ↓          ↓
WebController  ApiController
(Vue Blade)    (JSON Response)
```

### Exemple Pratique

#### ❌ MAUVAIS : Duplication de Code

```php
// WebController
public function index() {
    $products = Product::where('stock', '>', 0)->get();
    return view('products', compact('products'));
}

// ApiController
public function index() {
    $products = Product::where('stock', '>', 0)->get(); // DUPLICATION!
    return response()->json($products);
}
```

#### ✅ BON : Service Layer

```php
// ProductService.php
class ProductService {
    public function getAvailableProducts() {
        return Product::where('stock_quantity', '>', 0)
            ->with(['category', 'images'])
            ->latest()
            ->get();
    }
}

// Web/ProductController.php
public function index(ProductService $service) {
    $products = $service->getAvailableProducts();
    return view('products.index', compact('products'));
}

// Api/ProductController.php
public function index(ProductService $service) {
    $products = $service->getAvailableProducts();
    return response()->json([
        'success' => true,
        'data' => $products
    ]);
}
```

### Architecture des Routes API

**Structure** :
```
routes/
├── web.php          # Routes web (retournent des vues)
└── api.php          # Routes API (retournent du JSON)
```

**Exemple** :
```php
// routes/web.php
Route::get('/products', [Web\ProductController::class, 'index']);

// routes/api.php
Route::get('/products', [Api\ProductController::class, 'index']);
```

### Controllers Organization

```
app/Http/Controllers/
├── Web/                    # Controllers web (views)
│   ├── ProductController.php
│   ├── CartController.php
│   └── OrderController.php
│
└── Api/                    # Controllers API (JSON)
    ├── ProductController.php
    ├── CartController.php
    └── OrderController.php
```

### Services : Responsabilités

**Un Service doit** :
- ✅ Contenir la logique métier pure
- ✅ Être réutilisable (web, api, CLI, etc.)
- ✅ Gérer les transactions database
- ✅ Valider les règles métier
- ✅ Retourner des données ou objets

**Un Service ne doit PAS** :
- ❌ Retourner des vues (HTML)
- ❌ Retourner du JSON
- ❌ Gérer les redirections
- ❌ Accéder à `Request` directement

### Controllers : Responsabilités

**Un Controller doit** :
- ✅ Valider les inputs HTTP (`FormRequest`)
- ✅ Appeler le Service approprié
- ✅ Formatter la réponse (view ou JSON)
- ✅ Gérer les redirections/responses HTTP

**Un Controller ne doit PAS** :
- ❌ Contenir de la logique métier
- ❌ Faire des requêtes database directes (sauf très simple)
- ❌ Gérer des transactions complexes

### API Response Format Standard

```php
// Success Response
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}

// Error Response
{
    "success": false,
    "message": "Error description",
    "errors": { ... }
}
```

### Authentification API

**Utiliser Laravel Sanctum** :
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [Api\CartController::class, 'index']);
    Route::post('/cart/add', [Api\CartController::class, 'add']);
});
```

---

## Règles de Design & UX

### Identité Visuelle

#### Palette de Couleurs - BRAND COLORS

> [!IMPORTANT]
> **TOUJOURS RESPECTER LES COULEURS DU LOGO** : `assets/img/branding/logo.png`

Couleurs officielles extraites du logo :
- **Brand Gold**: `#B8860B` (principale)
- **Brand Gold Light**: `#D4AF37` (claire)
- **Brand Grey**: `#808080` (principale)
- **Brand Grey Dark**: `#505050` (foncée)

**Gradients à utiliser** :
- **Primary Gradient**: `linear-gradient(135deg, #B8860B 0%, #D4AF37 100%)`
- **Secondary Gradient**: `linear-gradient(135deg, #808080 0%, #A9A9A9 100%)`

**Règle d'Or** : Avant toute stylisation, consulter le logo et extraire/utiliser ces couleurs.

#### Style Moderne
- ✅ Utiliser des **gradients** or/gris pour les éléments importants
- ✅ **Glassmorphisme** léger pour la navbar
- ✅ **Animations fluides** (hover effects, transitions)
- ✅ **Border-radius arrondis** (20px pour cartes, 50px pour boutons)
- ✅ **Box-shadows** pour donner de la profondeur
- ✅ **Design responsive** (mobile-first)
- ✅ **Logo visible** : Utiliser le logo image, pas juste le texte

#### Principes UX
1. **User-friendly** : Interface intuitive et claire
2. **Professionnel** : Aspect sérieux pour inspirer confiance (fintech)
3. **Moderne** : Design actuel, pas basique
4. **Réaliste** : Éviter les placeholders, utiliser de vraies données
5. **Brand Cohérence** : Couleurs du logo partout

---

## Règles Métier Importantes

### Navigation & Accès

1. **Navigation Produits** : 
   - ✅ Tout le monde peut parcourir la boutique SANS connexion
   - ❌ Seuls les utilisateurs connectés peuvent ajouter au panier et acheter
   - 📢 Afficher clairement cette restriction aux visiteurs

2. **Système de Vente** :
   - Nécessite une **licence vendeur** (achat avec wallet)
   - Middleware `EnsureUserIsActiveSeller` protège les routes vendeur

3. **Transactions** :
   - Toutes les transactions passent par le **wallet**
   - KYC peut être requis pour certaines opérations
   - Système de confirmation de livraison

### Authentification & Autorisation

- **Fortify** est utilisé pour l'authentification
- Routes de login/register standard Laravel
- **Middleware** :
  - `auth` : Utilisateur connecté
  - `verified` : Email vérifié
  - `EnsureUserIsActiveSeller` : Vendeur actif
  - `can:admin-access` : Accès admin

---

## Modules Clés

### 1. Wallet (Fintech)
**Controllers**: `WalletController`  
**Routes**: `/wallet/recharge`, `/wallet/process-recharge`  
**Importance**: ⭐⭐⭐⭐⭐ (Cœur du système fintech)

### 2. KYC (Fintech)
**Controllers**: `KycController`, `AdminKycController`  
**Routes**: `/kyc`, `/admin/kyc`  
**Importance**: ⭐⭐⭐⭐⭐ (Sécurité et conformité)

### 3. Marketplace (E-commerce)
**Controllers**: `MarketplaceController`  
**Routes**: `/boutique`, `/boutique/{product}`  
**Importance**: ⭐⭐⭐⭐⭐ (Vitrine principale)

### 4. Checkout (E-commerce)
**Controllers**: `CheckoutController`  
**Routes**: `/cart/*`, `/checkout/process`  
**Importance**: ⭐⭐⭐⭐⭐ (Conversion)

### 5. Seller System (E-commerce + Fintech)
**Controllers**: `SellerController`, `SellerEspaceBoutiqueController`  
**Routes**: `/seller/*`  
**Importance**: ⭐⭐⭐⭐ (Écosystème vendeurs)

---

## Messages & Communication

### Langue
- Interface utilisateur : **Français**
- Documentation code : Anglais accepté
- Messages d'erreur : Français
- Email/Notifications : Français

### Ton de Communication
- **Page d'accueil** : Professionnel, moderne, inspirant confiance
- **Dashboard** : Clair, informatif, orienté action
- **Erreurs** : Poli, guidant vers la solution
- **Fintech** : Sérieux, rassurant, sécuritaire

---

## Décisions de Design Récentes

### Page d'Accueil (welcome.blade.php) - Février 2026
- ✅ Design moderne avec gradients purple/blue
- ✅ Navbar avec boutons Connexion/S'inscrire
- ✅ Hero section mettant en avant la double nature E-commerce + Fintech
- ✅ 8 feature cards (mix e-commerce/fintech)
- ✅ Section stats avec gradient background
- ✅ Footer dark moderne avec réseaux sociaux
- ✅ Notifications claires pour connexion requise
- ✅ Animations CSS (float, hover effects)
- ✅ Responsive design mobile-first

---

## Points d'Attention pour les Agents

### ⚠️ À NE PAS OUBLIER

1. **Nature Hybride** : Toujours considérer les aspects E-commerce ET Fintech
2. **Wallet-First** : Les transactions passent par le wallet, pas de paiement direct
3. **KYC Important** : Vérification d'identité = confiance de la plateforme
4. **Design Moderne** : Ne jamais créer d'UI basique ou "MVP minimal"
5. **Messages en Français** : Toujours utiliser le français pour l'interface
6. **Navigation Libre** : Ne pas bloquer la consultation produits, seulement l'achat
7. **🎨 Couleurs du Logo** : TOUJOURS respecter la palette or/gris du logo officiel
8. **🔄 Routes Web = Routes API** : Pour toute route web, créer également l'API équivalente
9. **📦 Service Layer OBLIGATOIRE** : Utiliser des Services pour éviter duplication entre Web et API controllers
10. **📄 Rapport Technique À Jour** : Après CHAQUE tâche complétée, mettre à jour `document/RAPPORT_TECHNIQUE_2026.md`

### 🎯 Objectifs Stratégiques

- Inspirer **confiance** (aspect fintech)
- Faciliter la **découverte** produits (aspect e-commerce)
- Simplifier les **transactions** (wallet intégré)
- Encourager l'**écosystème vendeur** (licence accessible)

---

## Contact & Support

**Email**: support@jouan-sugu.com  
**Téléphone**: +243 XX XXX XXXX  
**Localisation**: Kinshasa, RDC

---

## Système de Protection par Features (Contrôle d'Accès Granulaire)

> [!IMPORTANT]
> **SYSTÈME CRITIQUE** : Toutes les pages sensibles et routes doivent être protégées par le système de features. Ce système remplace les vérifications de permissions manuelles.

### Architecture du Système

Le système de protection se base sur **4 couches** :

```
┌─────────────┐
│   Modules   │ ← Regroupement logique (ex: admin, seller, wallet)
└──────┬──────┘
       │
┌──────▼──────┐
│  Features   │ ← Actions granulaires (ex: admin.roles.create)
└──────┬──────┘
       │
┌──────▼───────┐
│ Permissions  │ ← Groupes de features (ex: "Gestion Rôles")
└──────┬───────┘
       │
┌──────▼──────┐
│    Roles    │ ← Assignés aux users (ex: "Super-Admin")
└─────────────┘
```

### Composants Blade de Protection

#### 1. `<x-feature-section>` - Protection de Sections
Protège des blocs entiers (tables, groupes de champs, sections de page).

```blade
<x-feature-section feature="admin.roles.view-list" showDeniedMessage="true">
    <table>
        <!-- Tableau protégé -->
    </table>
</x-feature-section>
```

**Paramètres** :
- `feature` (requis) : Nom de la feature (format: `module.entity.action`)
- `showDeniedMessage` (optionnel, default: false) : Afficher un message si accès refusé

#### 2. `<x-feature-link>` - Protection de Liens
Protège les liens de navigation et d'action.

```blade
<x-feature-link 
    feature="admin.roles.edit" 
    route="{{ route('admin.roles.edit', $role->id) }}" 
    class="btn btn-sm btn-primary">
    <i class="bx bx-edit"></i> {{ __('Edit') }}
</x-feature-link>
```

**Paramètres** :
- `feature` (requis) : Nom de la feature
- `route` (requis) : URL de destination
- `hideIfDenied` (optionnel, default: true) : Cacher le lien si accès refusé

#### 3. `<x-feature-button>` - Protection de Boutons
Protège les boutons d'action (delete, submit, etc.).

```blade
<x-feature-button 
    feature="admin.roles.delete" 
    type="submit" 
    class="btn btn-danger">
    <i class="bx bx-trash"></i> {{ __('Delete') }}
</x-feature-button>
```

**Paramètres** :
- `feature` (requis) : Nom de la feature
- `hideIfDenied` (optionnel, default: true) : Cacher le bouton si accès refusé

### Middleware Global

- **Middleware** : `CheckFeatureAccess` (enregistré globalement)
- **Service** : `FeatureAccessService` (logique centralisée)
- **Helper** : `can_access_feature($user, $feature)` (global)

**Logique Permissive par Défaut** :
- ✅ Feature non trouvée → AUTORISER
- ✅ Feature sans permission → AUTORISER
- ✅ Permission sans rôle assigné → AUTORISER
- ❌ Permission avec rôle, user sans rôle → REFUSER

### Nomenclature des Features

**Format Standard** : `<module>.<entity>.<action>`

**Exemples** :
- Routes: `admin.roles.index`, `admin.roles.create`, `admin.roles.edit`
- Buttons: `admin.roles.delete`, `admin.permissions.create`
- Sections: `admin.roles.view-list`, `admin.roles.form-fields`
- Form Parts: `admin.modules.form-basic-info`, `admin.modules.form-settings`

**Conventions** :
- **Modules** : admin, seller, user, wallet
- **Actions CRUD** : index, create, store, show, edit, update, destroy
- **Actions UI** : view-list, view-details, manage-X, assign-X
- **Form Parts** : form-fields, form-basic-info, form-settings, form-appearance

### Patterns de Protection par Type de Page

#### Pattern 1: Pages Index
```blade
<!-- Header avec bouton Create -->
<div class="card-header">
    <h5>{{ __('Roles List') }}</h5>
    <x-feature-link feature="admin.roles.create" route="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> {{ __('Add New Role') }}
    </x-feature-link>
</div>

<!-- Table complète protégée -->
<x-feature-section feature="admin.roles.view-list" showDeniedMessage="true">
    <table>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>
                    <!-- Actions Edit/Delete -->
                    <x-feature-link feature="admin.roles.edit" route="{{ route('admin.roles.edit', $role) }}">
                        <i class="bx bx-edit"></i>
                    </x-feature-link>
                    
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}">
                        @csrf @method('DELETE')
                        <x-feature-button feature="admin.roles.delete" type="submit">
                            <i class="bx bx-trash"></i>
                        </x-feature-button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</x-feature-section>
```

#### Pattern 2: Pages Create/Edit (Formulaires)
```blade
<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf
    
    <!-- Groupe 1: Informations de base -->
    <x-feature-section feature="admin.roles.form-basic-info">
        <div class="mb-3">
            <label>{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="mb-3">
            <label>{{ __('Description') }}</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
    </x-feature-section>
    
    <!-- Groupe 2: Paramètres avancés (optionnel) -->
    <x-feature-section feature="admin.roles.form-settings">
        <div class="mb-3">
            <label>{{ __('Settings') }}</label>
            <!-- Champs avancés -->
        </div>
    </x-feature-section>
    
    <!-- Boutons Submit SANS protection (design intentionnel) -->
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
```

**⚠️ RÈGLE IMPORTANTE** : Les boutons "Save" / "Update" / "Submit" ne doivent PAS être protégés. Cela permet aux utilisateurs de tenter une soumission même s'ils n'ont pas accès aux champs. Le middleware côté serveur gère le refus réel.

#### Pattern 3: Pages d'Assignment (Rôles/Permissions)
```blade
<form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}">
    @csrf
    
    <x-feature-section feature="admin.roles.assign-permissions">
        <div class="mb-3">
            <input type="checkbox" id="select-all">
            <label for="select-all">{{ __('Select All') }}</label>
        </div>
        
        @foreach($permissions as $permission)
            <div class="form-check">
                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                <label>{{ $permission->name }}</label>
            </div>
        @endforeach
    </x-feature-section>
    
    <!-- Submit sans protection -->
    <button type="submit">{{ __('Update Permissions') }}</button>
</form>
```

### Commande de Scan `features:scan`

**Usage** :
```bash
php artisan features:scan
```

**Fonctionnement** :
1. Parcourt toutes les routes nommées
2. Scanne tous les fichiers Blade pour composants `<x-feature-*>`
3. Détecte automatiquement les features utilisées
4. Crée/met à jour les features dans la base de données
5. Associe automatiquement aux modules existants

**À Exécuter** :
- ✅ Après création de nouvelles pages
- ✅ Après ajout de nouvelles routes
- ✅ Après modification des composants de protection
- ✅ Avant mise en production

### Workflow de Développement avec Protection

#### Nouvelle Page Admin
```
1. Créer la page Blade (ex: admin/roles/create.blade.php)
2. Protéger les éléments avec <x-feature-section>
3. Protéger les liens/boutons avec <x-feature-link> et <x-feature-button>
4. Créer la route nommée (ex: 'admin.roles.create')
5. Lancer php artisan features:scan
6. Configurer les permissions dans l'interface admin
7. Tester avec différents profils
```

#### Nouvelle Route
```
1. Ajouter la route dans routes/web.php avec nom
   Route::get('/admin/roles/create', [RoleController::class, 'create'])
        ->name('admin.roles.create');

2. Lancer php artisan features:scan
   → La feature 'admin.roles.create' sera automatiquement créée

3. Assigner la feature à une permission dans l'admin
4. Assigner la permission à un rôle
5. Tester l'accès
```

### Helpers Globaux

```php
// Vérifier accès à une feature
if (can_access_feature(auth()->user(), 'admin.roles.create')) {
    // Autoriser
}

// Message d'erreur standardisé
$message = feature_auth_message('admin.roles.create');
// → "You do not have permission to access this feature: admin.roles.create"
```

### Configuration des Permissions (Interface Admin)

1. **Admin > Modules** : Gérer les modules (admin, seller, wallet, etc.)
2. **Admin > Features** : Visualiser toutes les features détectées
3. **Admin > Permissions** : Créer des groupes de features
4. **Admin > Roles** : Créer des rôles et assigner des permissions
5. **Admin > Users > Manage Roles** : Assigner des rôles aux utilisateurs

### Exemples Réels de Protection

**Pages déjà protégées (20+)** :
- ✅ Admin: profils, modules, features, permissions, roles, users, kyc-management
- ✅ Seller: product_form, espace_boutique
- ✅ User: profile, kyc
- ✅ Wallet: recharge

**Total features en base** : 101+ (après scan)

---

## Notes pour les Futures Fonctionnalités

_Cette section peut être mise à jour au fur et à mesure de l'évolution du projet_

- [ ] Système de notation vendeurs
- [ ] Programme de fidélité avec points
- [ ] API publique pour intégrations
- [ ] Application mobile (Android/iOS)
- [ ] Système de chat support en temps réel
- [ ] Multi-devises (USD, CDF, EUR)
- [ ] Système de parrainage

---

**Dernière mise à jour** : 2026-02-02  
**Version du contexte** : 2.0
**Changements majeurs v2.0** :
- Ajout des couleurs officielles du logo (or/gris)
- Règle obligatoire : Routes Web = Routes API
- Architecture Service Layer pour éviter duplication code

---

## Internationalisation (i18n)

> [!IMPORTANT]
> **ANTI-HALLUCINATION**: L'application supporte UNIQUEMENT 2 langues : **Français (fr)** et **Anglais (en)**. Ne JAM AIS assumer d'autres langues sans confirmation explicite.

### Fichiers de Traduction
- `lang/en.json` - 80+ clés anglaises
- `lang/fr.json` - 80+ clés françaises

### Middleware & Contrôleur
- **SetLocale** : `app/Http/Middleware/Set Locale.php` (enregistré dans `bootstrap/app.php`)
- **LanguageController** : Route `GET /lang/{locale}` (accepte 'en' ou 'fr')

### Utilisation
```blade
{{-- Vue Blade --}}
{{ __('Dashboard') }}
@section('title', __('Profile Management'))

{{-- Contrôleur --}}
return redirect()->with('success', __('Profile created successfully.'));
```

### Sélecteur de Langue
- **Emplacement** : Navbar (lignes 52-71)
- **Drapeaux** : 🇫🇷 French / 🇺🇸 English

### DataTables Localisation
- **Fichier local** : `public/assets/vendor/libs/datatables-bs5/i18n/fr-FR.json`
- **NE PAS** utiliser de CDN pour les traductions DataTables

---

## Gates & Permissions

> [!IMPORTANT]
> **ANTI-HALLUCINATION**: Utiliser Gates Laravel + middleware `can:` au lieu de vérifications manuelles dans les contrôleurs.

### Gates Définis (AppServiceProvider)
```php
Gate::define('admin-access', fn($user) => $user->isSuperAdmin());
Gate::define('super-admin-access', fn($user) => $user->isSuperAdmin());
```

### Utilisation Routes
```php
// Tous les admins
Route::middleware('can:admin-access')->group(...);

// Super-Admin UNIQUEMENT
Route::middleware('can:super-admin-access')->group(...);
```

### Système de Profils
- Table `profils` (UUID, libellé unique)
- User a un `profil_id` (remplace l'ancien champ `role`)
- Profils par défaut : Super-Admin, utilisateur, Agent de support, Verificateur kyc, Admin-entreprise

**Méthodes Helper** :
- `$user->isSuperAdmin()` - Vérifie le profil
- `$user->hasRole('role-slug')` - Vérifie les rôles granulaires

---

## 🚫 ANTI-HALLUCINATIONS CRITIQUES

1. **Langues** : Seules FR et EN sont supportées
2. **CDN DataTables** : Fichiers locaux uniquement, pas de CDN
3. **Permissions** : Gates + `can:` au lieu de méthodes dans contrôleurs
4. **Logique Métier** : Services, PAS dans les contrôleurs
5. **Profils** : Utiliser `profil_id`, pas `role`
6. **Traductions** : Tout en `__()` ou rien
7. **API Routes** : Créer routes API pour chaque route web

---

**Dernière mise à jour** : 2026-02-03  
**Version du contexte** : 3.0  
**Changements majeurs v3.0** :
- ✅ Système i18n complet (FR/EN) avec `__()`
- ✅ Gates Laravel pour permissions
- ✅ Système de profils (remplacement `role`)
- ✅ Anti-hallucination guards
- ✅ DataTables localisé (fichiers locaux)
