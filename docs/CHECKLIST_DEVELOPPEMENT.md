# Checklist de Développement - Système de Permissions

**À suivre pour CHAQUE nouvelle page/fonctionnalité**

## ✅ Checklist Obligatoire

### 1. **Routes** (Protection Automatique)

- [ ] Nommer TOUTES les routes avec `->name('...')`
- [ ] Utiliser une nomenclature cohérente : `{section}.{resource}.{action}`
  - ✅ Exemple : `admin.users.destroy`, `seller.products.create`
  - ❌ Éviter : `deleteUser`, `page1`, `test`

```php
// ✅ BON
Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->name('admin.users.destroy');

// ❌ MAUVAIS (pas de nom = pas de protection)
Route::delete('/users/{id}', [UserController::class, 'destroy']);
```

---

### 2. **Boutons** (Actions Sensibles)

Pour TOUT bouton qui effectue une action importante :

- [ ] Créer/Modifier
- [ ] Supprimer
- [ ] Approuver/Rejeter
- [ ] Exporter/Importer
- [ ] Actions financières

```blade
{{-- ✅ BON --}}
<x-feature-button feature="users.delete" class="btn btn-danger">
    <i class="bx bx-trash"></i> {{ __('Delete') }}
</x-feature-button>

{{-- ❌ MAUVAIS (bouton non protégé) --}}
<button class="btn btn-danger">Delete</button>
```

---

### 3. **Liens** (Navigation Protégée)

Pour les liens vers des pages sensibles :

- [ ] Édition de ressources
- [ ] Pages d'administration
- [ ] Rapports/Statistiques
- [ ] Configuration système

```blade
{{-- ✅ BON --}}
<x-feature-link feature="products.edit" route="{{ route('products.edit', $product) }}" class="dropdown-item">
    <i class="bx bx-edit"></i> {{ __('Edit') }}
</x-feature-link>

{{-- ❌ MAUVAIS (lien non protégé) --}}
<a href="{{ route('products.edit', $product) }}">Edit</a>
```

---

### 4. **Sections** (Parties de Vues)

Pour les sections contenant des informations sensibles :

- [ ] Statistiques financières
- [ ] Données personnelles sensibles
- [ ] Rapports avancés
- [ ] Informations confidentielles
- [ ] Widgets d'administration

```blade
{{-- ✅ BON --}}
<x-feature-section feature="dashboard.financial" showDeniedMessage="true">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <h5>{{ __('Revenue') }}</h5>
                <p>{{ $revenue }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <h5>{{ __('Profit') }}</h5>
                <p>{{ $profit }}</p>
            </div>
        </div>
    </div>
</x-feature-section>

{{-- ❌ MAUVAIS (section sensible non protégée) --}}
<div class="row">
    <div class="col-md-4">
        <!-- Données financières visibles par tous ! -->
    </div>
</div>
```

---

## 📋 Workflow de Développement

### Phase 1 : Développement

```
1. Créer la route avec nom
   Route::get('/rapport', [ReportController::class,'index'])->name('reports.index')

2. Créer la vue avec protections
   - <x-feature-button> pour actions
   - <x-feature-link> pour navigation
   - <x-feature-section> pour sections sensibles

3. Tester fonctionnalité
```

### Phase 2 : Scanner

```bash
php artisan features:scan
```

Vérifie dans `/admin/features` que toutes les features sont bien détectées :
- Routes : `reports.index`
- UI Elements : `reports.export`, `reports.view.financial`
- Sections : `dashboard.financial`

### Phase 3 : Configuration (Plus tard)

```
1. Créer permissions
   /admin/permissions/create → Grouper features logiques

2. Assigner aux rôles
   /admin/roles/{role}/permissions → Cocher permissions

3. Tester restrictions
   Se connecter avec user sans rôle → Vérifier accès refusé
```

---

## 🎯 Exemples par Type de Page

### Page CRUD Standard

```blade
{{-- index.blade.php --}}
<x-feature-link feature="products.create" route="{{ route('products.create') }}" class="btn btn-primary">
    <i class="bx bx-plus"></i> {{ __('Add Product') }}
</x-feature-link>

@foreach($products as $product)
    <x-feature-link feature="products.edit" route="{{ route('products.edit', $product) }}">
        Edit
    </x-feature-link>
    
    <x-feature-button feature="products.delete" class="btn btn-danger">
        Delete
    </x-feature-button>
@endforeach
```

### Tableau de Bord (Dashboard)

```blade
{{-- dashboard.blade.php --}}

{{-- Section financière protégée --}}
<x-feature-section feature="dashboard.financial">
    <div class="row">
        <!-- KPIs financiers -->
    </div>
</x-feature-section>

{{-- Section statistiques vendeur protégée --}}
<x-feature-section feature="dashboard.seller-stats">
    <div class="card">
        <!-- Stats vendeurs -->
    </div>
</x-feature-section>

{{-- Section publique (pas de protection) --}}
<div class="row">
    <div class="col-md-12">
        <!-- Bienvenue, infos générales -->
    </div>
</div>
```

### Page Administration

```blade
{{-- admin/users/index.blade.php --}}

<x-feature-link feature="users.create" route="{{ route('users.create') }}" class="btn btn-primary">
    Add User
</x-feature-link>

<x-feature-section feature="users.advanced-filters">
    <div class="card">
        <h5>{{ __('Advanced Filters') }}</h5>
        <!-- Filtres avancés -->
    </div>
</x-feature-section>

<table>
    @foreach($users as $user)
        <tr>
            <td>
                <x-feature-button feature="users.activate" class="btn btn-sm">
                    Activate
                </x-feature-button>
                
                <x-feature-button feature="users.delete" class="btn btn-sm btn-danger">
                    Delete
                </x-feature-button>
            </td>
        </tr>
    @endforeach
</table>
```

---

## 🚨 Points de Vigilance

### ⚠️ Ne PAS Protéger

- Navigation principale (menu public)
- Pages d'accueil publiques
- Footer, header
- Pages "À propos", "Contact"
- Routes de connexion/inscription

### ✅ TOUJOURS Protéger

- Actions de modification/suppression
- Exports de données
- Pages d'administration
- Statistiques/Rapports
- Actions financières
- Validation/Approbation

---

## 🔍 Auto-Vérification

Avant de pousser du code, vérifier :

```bash
# 1. Scanner features
php artisan features:scan

# 2. Lister routes sans nom
php artisan route:list --columns=name,uri,action | grep -v "name"

# 3. Chercher boutons/liens non protégés
grep -r "<button" resources/views/pages --exclude-dir=components | grep -v "x-feature-button"
grep -r "<a.*route(" resources/views/pages | grep -v "x-feature-link"
```

---

## 📝 Convention de Nommage

### Features de Routes
Format : `{section}.{resource}.{action}`

Exemples :
- `admin.users.index`
- `admin.users.create`
- `admin.users.destroy`
- `seller.products.edit`
- `reports.financial.export`

### Features de Boutons/Liens
Format : `{resource}.{action}`

Exemples :
- `users.delete`
- `products.edit`
- `orders.approve`
- `reports.export`

### Features de Sections
Format : `{page}.{section}` ou `{resource}.{view-type}`

Exemples :
- `dashboard.financial`
- `dashboard.seller-stats`
- `reports.advanced-filters`
- `users.activity-log`

---

## 🎓 Formation Équipe

### Pour les Nouveaux Développeurs

1. Lire ce document
2. Consulter `/docs/MIDDLEWARE_PERMISSIONS.md`
3. Exemples : Voir pages existantes dans `resources/views/pages/admin`
4. Tester : Créer une page simple avec protections
5. Scanner : `php artisan features:scan`
6. Vérifier : Dans `/admin/features`

### Code Review Checklist

- [ ] Routes nommées ?
- [ ] Boutons sensibles protégés avec `<x-feature-button>` ?
- [ ] Liens protégés avec `<x-feature-link>` ?
- [ ] Sections sensibles avec `<x-feature-section>` ?
- [ ] Features scannées ?
- [ ] Testé avec user sans permissions ?

---

## 🔧 Outils de Debug

### Vérifier Permission d'un User

```php
$user = User::find(1);
$service = app(\App\Services\FeatureAccessService::class);
$canAccess = $service->canAccess($user, 'users.delete');
dd($canAccess); // true ou false
```

### Lister Features d'une Page

```php
$features = \App\Models\Feature::where('slug', 'like', 'admin.users.%')->get();
foreach($features as $feature) {
    echo "{$feature->slug} - {$feature->type}\n";
}
```

---

## 📊 Métriques de Qualité

**Objectifs par Page** :

- ✅ 100% des routes nommées
- ✅ 100% des boutons CRUD protégés
- ✅ 80%+ des liens navigation protégés
- ✅ 100% des sections sensibles protégées
- ✅ Features scannées après chaque PR

**Indicateurs** :

```bash
# Nombre de routes sans nom
php artisan route:list | grep -c "Closure"

# Nombre de features
php artisan tinker
>>> \App\Models\Feature::count()

# Features par type
>>> \App\Models\Feature::groupBy('type')->selectRaw('type, count(*) as count')->get()
```

---

## 🚀 Résumé Ultra-Rapide

**Pour chaque nouvelle page** :

1. Routes → `->name('section.resource.action')`
2. Boutons actions → `<x-feature-button feature="...">`
3. Liens navigation → `<x-feature-link feature="...">`
4. Sections sensibles → `<x-feature-section feature="...">`
5. Scanner → `php artisan features:scan`
6. ✅ DONE !

**Protection activée automatiquement quand permissions configurées !** 🎯
