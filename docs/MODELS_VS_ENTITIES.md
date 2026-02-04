# Models vs Entities - Guide Pratique

**Date**: 2026-02-04

---

## 🤔 Model = Entities ?

### Réponse : **OUI et NON** (selon approche)

---

## 📚 Théorie DDD Pure

### DDD Strict (Séparation Complète)

**Entities** (Domain Layer) ≠ **Models** (Infrastructure Layer)

#### Entities (Logique Métier Pure)
```php
// app/Modules/Identity/Entities/User.php
namespace Modules\Identity\Entities;

class User  // POJO (Plain Old PHP Object)
{
    private string $id;
    private string $name;
    private Email $email;
    
    public function __construct(string $id, string $name, Email $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
    
    // Logique métier
    public function changeEmail(Email $newEmail): void
    {
        if ($this->email->equals($newEmail)) {
            return;
        }
        $this->email = $newEmail;
        // Domain Event
        $this->recordEvent(new EmailChanged($this->id));
    }
    
    // Pas de save(), pas de DB, code pur
}
```

#### Models (Data Mapper Eloquent)
```php
// app/Modules/Identity/Infrastructure/Models/UserModel.php (ou juste Models/)
namespace Modules\Identity\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model  // Eloquent ORM
{
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
    
    // Juste le mapping DB, pas de logique métier
}
```

#### Repository (Fait le Pont)
```php
// app/Modules/Identity/Repositories/UserRepository.php
class UserRepository
{
    public function save(User $entity): void
    {
        // Mapper Entity → Model
        $model = UserModel::findOrNew($entity->id());
        $model->name = $entity->name();
        $model->email = $entity->email()->toString();
        $model->save();
    }
    
    public function findById(string $id): ?User
    {
        $model = UserModel::find($id);
        if (!$model) return null;
        
        // Mapper Model → Entity
        return new User(
            $model->id,
            $model->name,
            Email::fromString($model->email)
        );
    }
}
```

**Avantages** :
- ✅ Séparation stricte Domain/Infrastructure
- ✅ Entities 100% indépendantes de Laravel
- ✅ Tests unitaires ultra-rapides (pas de DB)
- ✅ Portabilité (changer ORM sans toucher Domain)

**Inconvénients** :
- ❌ Double code (Entity + Model)
- ❌ Mapping Entity ↔ Model complexe
- ❌ Performance (conversions)
- ❌ Verbeux (beaucoup de boilerplate)

---

## 🎯 Approche Pragmatique Laravel (RECOMMANDÉE)

### Models = Entities "Riches" (Eloquent Models avec Logique)

**Un seul objet** qui fait les deux :
- ✅ Mapping DB (Eloquent)
- ✅ Logique métier (méthodes)

```php
// app/Modules/Identity/Entities/User.php
namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Identity\ValueObjects\Email;
use Modules\Identity\Events\EmailChanged;

class User extends Model  // Hérite d'Eloquent
{
    use HasUuids, SoftDeletes;
    
    protected $fillable = ['name', 'email', 'phone_number'];
    
    // ==========================================
    // PARTIE MODEL (Eloquent/DB)
    // ==========================================
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    // ==========================================
    // PARTIE ENTITY (Logique Métier)
    // ==========================================
    
    public function changeEmail(string $newEmail): void
    {
        $emailVO = Email::fromString($newEmail);
        
        if ($this->email === $emailVO->toString()) {
            return;
        }
        
        $this->email = $emailVO->toString();
        $this->email_verified_at = null; // Reset verification
        
        // Domain Event
        event(new EmailChanged($this->id, $newEmail));
    }
    
    public function assignRole(Role $role): void
    {
        if ($this->hasRole($role)) {
            return;
        }
        
        $this->roles()->attach($role->id);
        event(new RoleAssigned($this->id, $role->id));
    }
    
    public function hasRole(Role $role): bool
    {
        return $this->roles()->where('id', $role->id)->exists();
    }
    
    public function isSuperAdmin(): bool
    {
        return $this->profil && $this->profil->nom === 'Super-Admin';
    }
    
    public function canAccessFeature(string $featureName): bool
    {
        // Logique métier
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        foreach ($this->roles as $role) {
            if ($role->hasFeature($featureName)) {
                return true;
            }
        }
        
        return false;
    }
    
    // Validation métier
    public function suspend(string $reason): void
    {
        if ($this->isSuperAdmin()) {
            throw new \DomainException("Cannot suspend Super-Admin");
        }
        
        $this->status = 'suspended';
        $this->save();
        
        event(new UserSuspended($this->id, $reason));
    }
}
```

**Avantages** :
- ✅ **Simplicité** : Un seul objet User
- ✅ **Performance** : Pas de mapping Entity ↔ Model
- ✅ **Laravel native** : Utilise tout Eloquent (relations, scopes, etc.)
- ✅ **Pragmatique** : Parfait pour 95% des projets Laravel
- ✅ **Moins de code** : Pas de duplication

**Inconvénients** :
- ❌ Couplage Laravel (mais c'est OK pour un projet Laravel pur)
- ❌ Tests un peu plus lourds (besoin DB pour tester relations)

---

## 🎯 Ma Recommandation pour Ton Projet

### **Approche Pragmatique : Models = Entities Riches**

**Structure dans chaque module** :

```
app/Modules/Identity/
├── Entities/              # Eloquent Models AVEC logique métier
│   ├── User.php           # extends Model (Eloquent)
│   ├── Role.php           # extends Model
│   └── Permission.php     # extends Model
├── ValueObjects/          # Classes immuables
│   ├── Email.php          # Validation email
│   ├── PhoneNumber.php    # Validation téléphone
│   └── UserStatus.php     # Enum-like class
├── Services/              # Logique métier complexe
│   └── FeatureAccessService.php
├── Repositories/          # Abstraction (optionnel)
│   └── UserRepository.php
└── ...
```

### Pourquoi ?

1. **Tu utilises déjà Eloquent** : Pas besoin de réinventer la roue
2. **Application Laravel pure** : Pas de contrainte de portabilité
3. **Équipe Laravel** : Familier avec Eloquent
4. **Pragmatisme** : Focus sur features métier, pas sur architecture pure
5. **Productivité** : Livrer plus vite avec moins de code

### Quand Utiliser les Value Objects ?

Pour les **concepts métier importants** avec **règles de validation** :

```php
// ✅ Value Object pour Email (validation complexe)
$email = Email::fromString('user@example.com');

// ✅ Value Object pour Money (calculs précis)
$price = Money::fromFloat(99.99, Currency::USD());

// ❌ Pas besoin pour $name (simple string)
$user->name = 'John Doe'; // Direct
```

---

## 📋 Règles Pratiques

### Dans Tes Modules

#### 1. **Entities/** = Eloquent Models Enrichis
```php
class User extends Model
{
    // Relations Eloquent
    // + Méthodes métier
    // + Validation domaine
}
```

#### 2. **ValueObjects/** = Validation & Immuabilité
```php
final class Email
{
    // Pas d'extends Model
    // Juste validation + logique
}
```

#### 3. **Services/** = Logique Multi-Entities
```php
class OrderProcessingService
{
    // Coordonne Order + Wallet + Product
    // Transactions complexes
}
```

#### 4. **Repositories/** = Abstraction (Optionnel)
Si tu veux isoler les queries complexes :
```php
class UserRepository
{
    public function findActiveUsersWithRoles(): Collection
    {
        return User::with('roles')
            ->where('status', 'active')
            ->get();
    }
}
```

---

## ✅ Décision Finale

### **OUI : Models = Entities** (dans ton contexte)

**Structure Révisée** :

```
app/Modules/Identity/
├── Entities/              # Eloquent Models (= Entities riches)
│   ├── User.php           # extends Model + logique métier
│   ├── Profil.php
│   ├── Role.php
│   ├── Permission.php
│   ├── Module.php
│   └── Feature.php
├── ValueObjects/          # Validation & concepts métier
│   ├── Email.php
│   ├── PhoneNumber.php
│   └── FeatureName.php
├── DTOs/                  # Transfert de données
│   ├── CreateUserDTO.php
│   └── UpdateUserDTO.php
├── Services/              # Logique complexe
├── Repositories/          # Queries complexes (optionnel)
├── Controllers/
├── Views/
└── ...
```

### Code Exemple Complet

**User Entity (Eloquent + Logique)**
```php
<?php
namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shared\Traits\HasUuid;

class User extends Model
{
    use HasUuid, SoftDeletes;
    
    protected $fillable = ['name', 'email', 'phone_number', 'status'];
    
    // ==========================================
    // RELATIONS (Eloquent)
    // ==========================================
    
    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }
    
    public function wallet()
    {
        return $this->hasOne(\Modules\Fintech\Entities\Wallet::class);
    }
    
    // ==========================================
    // LOGIQUE MÉTIER (Domain)
    // ==========================================
    
    public static function register(string $name, string $email, string $password): self
    {
        $user = new self();
        $user->name = $name;
        $user->email = Email::fromString($email)->toString(); // Validation via VO
        $user->password = bcrypt($password);
        $user->status = 'active';
        $user->save();
        
        event(new \Modules\Identity\Events\UserRegistered($user->id));
        
        return $user;
    }
    
    public function assignRole(Role $role): void
    {
        if ($this->hasRole($role)) {
            return;
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
    
    public function canAccessFeature(string $featureName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        foreach ($this->roles as $role) {
            if ($role->hasPermissionForFeature($featureName)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function suspend(string $reason): void
    {
        if ($this->isSuperAdmin()) {
            throw new \DomainException("Cannot suspend Super-Admin");
        }
        
        $this->update(['status' => 'suspended']);
        event(new \Modules\Identity\Events\UserSuspended($this->id, $reason));
    }
    
    public function activate(): void
    {
        $this->update(['status' => 'active']);
        event(new \Modules\Identity\Events\UserActivated($this->id));
    }
}
```

---

## 🚀 Conclusion

**Pour Ton Projet** :
- ✅ **Models = Entities** (Eloquent Models enrichis)
- ✅ Un seul objet, deux responsabilités (DB + Logique)
- ✅ Pragmatique, performant, Laravel-friendly
- ✅ Value Objects pour concepts métier importants
- ✅ Services pour logique multi-entities

**On part sur cette base ?** 🎯
