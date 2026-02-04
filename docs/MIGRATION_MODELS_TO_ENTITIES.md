# Migration Models → Entities - Guide Pratique

**Date**: 2026-02-04

---

## 🎯 Réponse : NON, pas de dossier "Models/" séparé !

### Structure Actuelle (Avant Migration)
```
app/
└── Models/
    ├── User.php                    # Eloquent Model
    ├── Profil.php
    ├── Role.php
    ├── Permission.php
    ├── Module.php
    ├── Feature.php
    ├── Wallet.php
    ├── Transaction.php
    ├── Product.php
    ├── Order.php
    └── ...
```

### Structure Cible (Après Migration)
```
app/
└── Modules/
    ├── Identity/
    │   └── Entities/               # Anciens Models/ renommés + enrichis
    │       ├── User.php            # = app/Models/User.php (déplacé)
    │       ├── Profil.php
    │       ├── Role.php
    │       ├── Permission.php
    │       ├── Module.php
    │       └── Feature.php
    │
    ├── Fintech/
    │   └── Entities/
    │       ├── Wallet.php          # = app/Models/Wallet.php (déplacé)
    │       └── Transaction.php
    │
    ├── Marketplace/
    │   └── Entities/
    │       ├── Product.php         # = app/Models/Product.php (déplacé)
    │       ├── Order.php
    │       ├── Cart.php
    │       └── Category.php
    │
    └── Seller/
        └── Entities/
            ├── SellerProfile.php   # = app/Models/SellerProfile.php (déplacé)
            └── License.php
```

---

## 📦 Pas de Dossier "Models/" dans les Modules !

### ❌ FAUX (Ne PAS faire)
```
app/Modules/Identity/
├── Entities/               # ← Domain Entities (POJO)
│   └── User.php            # Code pur, pas d'Eloquent
├── Models/                 # ← Eloquent Models (DOUBLE !)
│   └── UserModel.php       # Mapping DB
└── Repositories/
    └── UserRepository.php  # Fait le pont Entity ↔ Model
```
**Problème** : Code dupliqué, mapping complexe, verbeux

### ✅ CORRECT (À faire)
```
app/Modules/Identity/
├── Entities/               # ← Eloquent Models ENRICHIS
│   └── User.php            # extends Model + logique métier
├── ValueObjects/           # ← Concepts métier
│   └── Email.php           # Validation
└── Repositories/           # ← Queries complexes (optionnel)
    └── UserRepository.php  # Abstraction si besoin
```
**Avantage** : Simple, direct, performant

---

## 🔄 Migration Concrète

### Étape 1 : Déplacer l'Ancien Model

**Avant** (app/Models/User.php) :
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email'];
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
```

**Après** (app/Modules/Identity/Entities/User.php) :
```php
<?php
namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Model;
use Shared\Traits\HasUuid;

class User extends Model  // Toujours extends Model !
{
    use HasUuid;
    
    protected $fillable = ['name', 'email', 'phone_number', 'status'];
    
    // ==========================================
    // RELATIONS (Eloquent - déjà existantes)
    // ==========================================
    
    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    // ==========================================
    // LOGIQUE MÉTIER (NOUVELLEMENT AJOUTÉE)
    // ==========================================
    
    public function assignRole(Role $role): void
    {
        if ($this->hasRole($role)) {
            return; // Déjà assigné
        }
        
        $this->roles()->attach($role->id);
    }
    
    public function hasRole(Role $role): bool
    {
        return $this->roles->contains('id', $role->id);
    }
    
    public function isSuperAdmin(): bool
    {
        return $this->profil?->nom === 'Super-Admin';
    }
    
    public function suspend(string $reason): void
    {
        if ($this->isSuperAdmin()) {
            throw new \DomainException("Cannot suspend Super-Admin");
        }
        
        $this->update(['status' => 'suspended']);
        event(new \Modules\Identity\Events\UserSuspended($this->id, $reason));
    }
}
```

### Étape 2 : Mettre à Jour les Namespaces

**Controller** (app/Modules/Identity/Controllers/Web/UserManagementController.php) :
```php
<?php
namespace Modules\Identity\Controllers\Web;

use Modules\Identity\Entities\User;  // ← Nouveau namespace
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('profil', 'roles')->get();  // ← Marche exactement pareil !
        return view('identity::users.index', compact('users'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([...]);
        
        $user = User::create($validated);  // ← Toujours Eloquent
        
        return redirect()->route('admin.users.index');
    }
}
```

### Étape 3 : Supprimer l'Ancien Dossier Models/

```bash
# Après avoir migré tous les Models vers les modules
rm -rf app/Models/
```

---

## 📋 Checklist Migration Models → Entities

### Pour Chaque Domaine

#### Identity
- [ ] Déplacer `app/Models/User.php` → `app/Modules/Identity/Entities/User.php`
- [ ] Changer namespace `App\Models` → `Modules\Identity\Entities`
- [ ] Ajouter logique métier (méthodes assignRole, etc.)
- [ ] Mettre à jour imports dans tous les Controllers
- [ ] Mettre à jour imports dans tous les Services
- [ ] Tests toujours fonctionnels

#### Fintech
- [ ] Déplacer `app/Models/Wallet.php` → `app/Modules/Fintech/Entities/Wallet.php`
- [ ] Déplacer `app/Models/Transaction.php` → `app/Modules/Fintech/Entities/Transaction.php`
- [ ] Changer namespaces
- [ ] Ajouter logique métier
- [ ] Mettre à jour imports
- [ ] Tests OK

#### Marketplace
- [ ] Déplacer `app/Models/Product.php` → `app/Modules/Marketplace/Entities/Product.php`
- [ ] Déplacer `app/Models/Order.php` → `app/Modules/Marketplace/Entities/Order.php`
- [ ] Déplacer `app/Models/Cart.php` → `app/Modules/Marketplace/Entities/Cart.php`
- [ ] Déplacer `app/Models/Category.php` → `app/Modules/Marketplace/Entities/Category.php`
- [ ] Changer namespaces
- [ ] Ajouter logique métier
- [ ] Mettre à jour imports
- [ ] Tests OK

#### Seller
- [ ] Déplacer `app/Models/SellerProfile.php` → `app/Modules/Seller/Entities/SellerProfile.php`
- [ ] Changer namespaces
- [ ] Ajouter logique métier
- [ ] Mettre à jour imports
- [ ] Tests OK

---

## 🎯 Résumé

### Ce que tu fais :
1. **Déplacer** : `app/Models/User.php` → `app/Modules/Identity/Entities/User.php`
2. **Renommer namespace** : `namespace App\Models;` → `namespace Modules\Identity\Entities;`
3. **Enrichir** : Ajouter méthodes métier (assignRole, isSuperAdmin, etc.)
4. **Mettre à jour imports** : Partout où on utilise `use App\Models\User;` → `use Modules\Identity\Entities\User;`

### Ce que tu ne fais PAS :
- ❌ Créer un dossier `Models/` séparé
- ❌ Dupliquer le code (Entity + Model)
- ❌ Créer des Repositories pour mapper Entity ↔ Model
- ❌ Changer la façon dont Eloquent fonctionne

### Le Résultat :
- ✅ Eloquent Models déplacés dans `Entities/`
- ✅ Namespace cohérent avec le module
- ✅ Logique métier ajoutée progressivement
- ✅ Tout continue de fonctionner comme avant (juste avec plus de logique)

---

**C'est clair maintenant ?** 🎯
