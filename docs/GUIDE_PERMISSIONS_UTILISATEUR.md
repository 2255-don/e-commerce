# Guide d'Utilisation - Système de Permissions Granulaires

Ce guide explique comment utiliser le système de permissions pour gérer les accès dans l'application.

---

## 📋 Table des Matières

1. [Concepts de Base](#concepts-de-base)
2. [Interface d'Administration](#interface-dadministration)
3. [Utilisation dans le Code](#utilisation-dans-le-code)
4. [Commandes Artisan](#commandes-artisan)
5. [Cas d'Usage Courants](#cas-dusage-courants)

---

## Concepts de Base

Le système de permissions est organisé en 4 niveaux hiérarchiques :

```
MODULES → FEATURES → PERMISSIONS → ROLES → USERS
```

### 🎨 **Modules**
Regroupements logiques de fonctionnalités (ex: Users, Products, Wallet)

### 🧩 **Features**
Éléments contrôlables individuellement :
- **Route** : Accès à une URL spécifique
- **Action** : Bouton ou action spécifique
- **UI Element** : Bouton, lien dans l'interface
- **Section** : Section entière d'une page

### 🔐 **Permissions**
Groupement de plusieurs features sous un nom logique (ex: "Gérer Utilisateurs")

### 👤 **Rôles**
Ensemble de permissions assignées à des utilisateurs

---

## Interface d'Administration

### Accès
Toutes les pages d'administration sont accessibles sous `/admin` et nécessitent les droits super-admin.

### Gestion des Modules
**URL** : `/admin/modules`

- Créer de nouveaux modules
- Définir icône et couleur (pour l'UI)
- Organiser par ordre d'affichage
- Marquer comme "core" (non supprimable)

### Gestion des Features
**URL** : `/admin/features`

- Voir toutes les features détectées automatiquement
- Filtrer par module ou type
- Modifier les descriptions
- **Note** : La plupart sont auto-détectées via `features:scan`

### Gestion des Permissions
**URL** : `/admin/permissions`

- Créer des permissions logiques
- Assigner plusieurs features à une permission
- Organiser par module

### Assignation aux Rôles
**URL** : `/admin/profils` → Menu "Manage Permissions"

1. Sélectionner un rôle
2. Cocher les permissions à assigner
3. Sauvegarder

---

## Utilisation dans le Code

### 1. Boutons Conditionnels

```blade
<x-feature-button feature="users.delete" class="btn btn-danger delete-btn">
    <i class="bx bx-trash"></i> {{ __('Delete') }}
</x-feature-button>
```

**Options** :
- `hideIfDenied` (défaut: true) : Cache complètement si refusé
- `hideIfDenied="false"` : Affiche en disabled avec message

### 2. Liens Conditionnels

```blade
<x-feature-link feature="products.edit" route="{{ route('products.edit', $product) }}" class="dropdown-item">
    <i class="bx bx-edit"></i> {{ __('Edit') }}
</x-feature-link>
```

### 3. Sections Conditionnelles

```blade
<x-feature-section feature="reports.financial" showDeniedMessage="true">
    <div class="card">
        <!-- Contenu réservé aux utilisateurs autorisés -->
        <h5>Rapport Financier</h5>
        ...
    </div>
</x-feature-section>
```

### 4. Vérification Programmatique

```php
// Dans un Controller
use App\Services\PermissionService;

public function __construct(PermissionService $permissionService)
{
    $this->permissionService = $permissionService;
}

public function show()
{
    if (!$this->permissionService->userCanAccessFeature(auth()->user(), 'users.view')) {
        abort(403);
    }
    
    // ...
}
```

### 5. Middleware (future implémentation)

```php
Route::get('/admin/reports', [ReportController::class, 'index'])
    ->middleware('feature:reports.view');
```

---

## Commandes Artisan

### Scanner les Features
```bash
php artisan features:scan
```
Détecte automatiquement toutes les routes et composants feature dans vos vues.

### Vérifier la Cohérence
```bash
php artisan features:check
php artisan features:check --fix  # Nettoie les features orphelines
```

### Créer un Module
```bash
php artisan make:module "Gestion Rapports" --icon=bx-chart --color=#D4AF37
```

### Générer les Permissions
```bash
php artisan permissions:generate rapports    # Pour un module
php artisan permissions:generate --all       # Pour tous les modules
```

---

## Cas d'Usage Courants

### Cas 1 : Ajouter une Nouvelle Fonctionnalité

**Scénario** : Vous ajoutez un bouton "Export CSV" dans la liste des utilisateurs.

1. **Ajouter le composant dans la vue** :
   ```blade
   <x-feature-button feature="users.export" class="btn btn-success">
       <i class="bx bx-download"></i> Export CSV
   </x-feature-button>
   ```

2. **Scanner** :
   ```bash
   php artisan features:scan
   ```

3. **Assigner à une permission** :
   - Aller sur `/admin/permissions`
   - Éditer la permission "Gérer Utilisateurs"
   - Cocher la feature "users.export"

4. **C'est fait !** Les rôles ayant la permission "Gérer Utilisateurs" verront le bouton.

### Cas 2 : Créer un Nouveau Module Complet

**Scénario** : Vous créez un module "Rapports" avec ses propres pages.

1. **Créer le module** :
   ```bash
   php artisan make:module "Rapports" --icon=bx-bar-chart --color=#D4AF37
   ```
   → Génère automatiquement 4 permissions (view, create, edit, delete)

2. **Créer vos routes** :
   ```php
   Route::prefix('rapports')->name('rapports.')->group(function () {
       Route::get('/', [RapportsController::class, 'index'])->name('index');
       Route::get('/create', [RapportsController::class, 'create'])->name('create');
   });
   ```

3. **Utiliser les composants dans vos vues** :
   ```blade
   <x-feature-section feature="rapports.index">
       <!-- Liste des rapports -->
   </x-feature-section>
   ```

4. **Scanner les features** :
   ```bash
   php artisan features:scan
   ```

5. **Assigner aux rôles** :
   - Aller sur `/admin/profils`
   - Choisir le rôle "Admin"
   - "Manage Permissions" → Cocher les permissions "Rapports"

### Cas 3 : Restreindre une Section Existante

**Scénario** : Vous voulez que seuls les admins voient le tableau de bord financier.

1. **Entourer la section** :
   ```blade
   <x-feature-section feature="dashboard.financial" showDeniedMessage="true">
       <div class="row">
           <!-- Widgets financiers -->
       </div>
   </x-feature-section>
   ```

2. **Scanner** :
   ```bash
   php artisan features:scan
   ```

3. **Créer une permission** :
   - `/admin/permissions/create`
   - Nom : "Voir Dashboard Financier"
   - Slug : `dashboard.financial.view`
   - Assigner la feature `dashboard.financial`

4. **Assigner au rôle Admin** :
   - `/admin/profils` → Admin → "Manage Permissions"
   - Cocher "Voir Dashboard Financier"

### Cas 4 : Nettoyer après Refactoring

**Scénario** : Vous avez supprimé des routes et voulez nettoyer la DB.

```bash
# Vérifier les features orphelines
php artisan features:check

# Nettoyer automatiquement
php artisan features:check --fix

# Re-scanner pour les nouvelles features
php artisan features:scan
```

---

## ⚠️ Bonnes Pratiques

### ✅ À FAIRE

1. **Scanner après chaque ajout de composant**
   ```bash
   php artisan features:scan
   ```

2. **Utiliser des slugs cohérents**
   - Format : `{module}.{action}`
   - Exemple : `users.create`, `products.delete`

3. **Grouper les features logiquement**
   - Une permission = un ensemble cohérent de features
   - Exemple : Permission "Gérer Produits" = create + edit + delete

4. **Tester en local avant déploiement**
   ```bash
   php artisan features:check
   ```

### ❌ À ÉVITER

1. **Ne pas créer de features manuellement**
   - Laissez `features:scan` les détecter automatiquement

2. **Ne pas supprimer les modules "core"**
   - Ils sont essentiels au fonctionnement de l'application

3. **Ne pas donner toutes les permissions à tous les rôles**
   - Principe du moindre privilège

---

## 🆘 Dépannage

### Le bouton ne s'affiche pas

1. Vérifier que la feature existe : `/admin/features`
2. Vérifier que le module du feature existe : `/admin/modules`
3. Vérifier que l'utilisateur a un rôle avec la bonne permission
4. Scanner à nouveau : `php artisan features:scan`

### Erreur "Module not found" lors du scan

1. Créer le module manquant :
   ```bash
   php artisan make:module "Nom du Module"
   ```
2. Re-scanner :
   ```bash
   php artisan features:scan
   ```

### Permissions ne s'appliquent pas

1. Vider le cache :
   ```bash
   php artisan cache:clear
   ```
2. Vérifier l'assignation dans `/admin/roles/{role}/permissions`

---

## 📞 Support

Pour toute question ou problème, consulter :
- Documentation complète : `/docs/COMMANDES_PERMISSIONS.md`
- Rapport technique : `RAPPORT_TECHNIQUE_2026.md`
