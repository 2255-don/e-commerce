# Identity Module

## 📋 Description

Module de gestion de l'identité et des accès utilisateurs. Responsable de l'authentification, l'autorisation, la gestion des rôles et permissions.

---

## 🎯 Responsabilités

- Gestion des utilisateurs (CRUD)
- Gestion des profils (Super-Admin, Admin, User, Seller)
- Gestion des rôles et permissions
- Système de features granulaires
- Système de modules (groupement de features)
- Contrôle d'accès basé sur les permissions

---

## 📦 Entities

### User (Aggregate Root)
- **Description**: Représente un utilisateur du système
- **Relations**: 
  - `belongsTo` Profil
  - `belongsToMany` Role
  - `hasOne` Wallet (Fintech)
  - `hasOne` SellerProfile (Seller)
- **Méthodes métier**:
  - `assignRole(Role $role): void`
  - `hasRole(Role $role): bool`
  - `isSuperAdmin(): bool`
  - `canAccessFeature(string $featureName): bool`
  - `suspend(string $reason): void`
  - `activate(): void`

### Profil
- **Description**: Type d'utilisateur (Super-Admin, Admin, User, Seller)
- **Relations**: `hasMany` User

### Role
- **Description**: Rôle assignable aux utilisateurs
- **Relations**: 
  - `belongsToMany` User
  - `belongsToMany` Permission
- **Méthodes métier**:
  - `assignPermission(Permission $permission): void`
  - `hasPermission(Permission $permission): bool`

### Permission
- **Description**: Permission regroupant plusieurs features
- **Relations**:
  - `belongsToMany` Role
  - `belongsToMany` Feature

### Module
- **Description**: Groupement logique de features (admin, seller, wallet, etc.)
- **Relations**: `hasMany` Feature

### Feature
- **Description**: Feature granulaire (admin.roles.create, etc.)
- **Relations**:
  - `belongsTo` Module
  - `belongsToMany` Permission

---

## 🔧 Value Objects

### Email
- **Description**: Valide et normalise les adresses email
- **Méthodes**: `fromString(string): Email`, `toString(): string`

### PhoneNumber
- **Description**: Valide et normalise les numéros de téléphone
- **Méthodes**: `fromString(string): PhoneNumber`, `toString(): string`

### UserStatus
- **Description**: Statut utilisateur (active, inactive, suspended)
- **Méthodes**: `active()`, `inactive()`, `suspended()`, `isActive(): bool`

### FeatureName
- **Description**: Nom de feature au format module.entity.action
- **Méthodes**: `fromString(string)`, `module()`, `entity()`, `action()`

---

## 🔍 Services

### FeatureAccessService
- **Description**: Service de vérification d'accès aux features
- **Méthodes**:
  - `canAccess(User $user, FeatureName $featureName): bool`
  - **Principe**: Permissif par défaut (accès autorisé sauf si explicitement refusé)

### PermissionManagementService
- **Description**: Service de gestion des permissions
- **Méthodes**:
  - `createPermission(string $name, array $featureIds): Permission`
  - `assignPermissionToRole(Permission $permission, Role $role): void`

### AuthorizationService
- **Description**: Service d'autorisation général
- **Méthodes**:
  - `authorize(User $user, string $feature): void` (throw si refusé)

---

## 📡 Controllers

### Web
- `UserManagementController`: CRUD utilisateurs
- `RoleController`: CRUD rôles
- `PermissionController`: CRUD permissions
- `FeatureController`: CRUD features
- `ModuleController`: CRUD modules
- `ProfilController`: CRUD profils

### API
- `UserApiController`: API REST pour utilisateurs
- `RoleApiController`: API REST pour rôles

---

## 🛣️ Routes

### Web Routes (web.php)
```php
Route::prefix('admin')->middleware(['auth', 'feature'])->group(function () {
    Route::resource('users', UserManagementController::class);
    Route::get('users/{user}/roles', [UserManagementController::class, 'roles']);
    
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions']);
    
    Route::resource('permissions', PermissionController::class);
    Route::resource('features', FeatureController::class);
    Route::resource('modules', ModuleController::class);
    Route::resource('profils', ProfilController::class);
});
```

---

## 🗄️ Migrations

- `xxxx_create_users_table.php`
- `xxxx_create_profils_table.php`
- `xxxx_create_roles_table.php`
- `xxxx_create_permissions_table.php`
- `xxxx_create_modules_table.php`
- `xxxx_create_features_table.php`
- `xxxx_create_role_user_table.php` (pivot)
- `xxxx_create_feature_permission_table.php` (pivot)
- `xxxx_create_permission_role_table.php` (pivot)

---

## 🎭 Events

### UserRegistered
- **Trigger**: Nouvel utilisateur créé
- **Listeners**: SendWelcomeEmail

### RoleAssigned
- **Trigger**: Rôle assigné à un utilisateur
- **Listeners**: LogRoleAssignment

### FeatureAccessDenied
- **Trigger**: Accès refusé à une feature
- **Listeners**: LogFeatureAccess

---

## ✅ Tests

### Unit Tests
- `UserEntityTest`: Tests logique métier User
- `EmailValueObjectTest`: Tests validation Email
- `PhoneNumberValueObjectTest`: Tests validation PhoneNumber
- `FeatureAccessServiceTest`: Tests service permissions

### Integration Tests
- `UserRepositoryTest`: Tests persistence User
- `RoleRepositoryTest`: Tests persistence Role

### Feature Tests
- `UserManagementTest`: Tests CRUD utilisateurs
- `RoleManagementTest`: Tests CRUD rôles
- `PermissionSystemTest`: Tests système permissions complet

---

## 📚 Usage

### Créer un Utilisateur
```php
use Modules\Identity\Entities\User;
use Modules\Identity\ValueObjects\Email;
use Modules\Identity\ValueObjects\PhoneNumber;

$user = new User();
$user->name = 'John Doe';
$user->email = Email::fromString('john@example.com')->toString();
$user->phone_number = PhoneNumber::fromString('+243 xxx')->toString();
$user->save();
```

### Assigner un Rôle
```php
$user = User::find($userId);
$role = Role::find($roleId);

$user->assignRole($role);
```

### Vérifier Accès Feature
```php
use Modules\Identity\Services\FeatureAccessService;
use Modules\Identity\ValueObjects\FeatureName;

$service = app(FeatureAccessService::class);
$canAccess = $service->canAccess(
    $user,
    FeatureName::fromString('admin.roles.create')
);
```

---

## 🔄 Dépendances

### Vers Autres Modules
- **Fintech**: User → hasOne Wallet
- **Seller**: User → hasOne SellerProfile

### Depuis Autres Modules
- Tous les modules utilisent Identity pour l'authentification

---

## 📝 Notes
- **Système permissif**: Par défaut, accès autorisé si feature non configurée
- **Super-Admin**: Bypass complet du système de permissions
- **Nomenclature features**: Toujours au format `module.entity.action`
