# Commandes Artisan - Système de Permissions

Ce document décrit toutes les commandes Artisan disponibles pour gérer le système de permissions granulaires.

---

## 🔍 features:scan

**Description :** Scanne les routes et vues Blade pour auto-détecter et enregistrer les features.

**Usage :**
```bash
php artisan features:scan
php artisan features:scan --fresh  # Supprime toutes les features avant de scanner
```

**Options :**
- `--fresh` : Supprime toutes les features existantes avant le scan (⚠️ DESTRUCTIF)

**Fonctionnement :**
- Scanne toutes les routes nommées Laravel
- Scanne les fichiers Blade pour détecter les composants `<x-feature-button>`, `<x-feature-section>`, `<x-feature-link>`
- Crée automatiquement les features dans la base de données
- Associe chaque feature à son module (détecté depuis le slug)

**Exemple de sortie :**
```
📍 Scanning routes...
  ✓ Created: users.index
  ✓ Created: users.create
📄 Scanning Blade views...
  ✓ Created: profils.delete

✅ Scan completed!
+---------------------------+-------+
| Routes scanned            | 77    |
| Components scanned        | 3     |
| New features registered   | 18    |
| Total features in DB      | 39    |
+---------------------------+-------+
```

---

## 🔧 features:check

**Description :** Vérifie la cohérence des features entre la base de données et le code.

**Usage :**
```bash
php artisan features:check
php artisan features:check --fix  # Supprime automatiquement les features orphelines
```

**Options :**
- `--fix` : Supprime automatiquement les features orphelines de la base de données

**Détections :**
1. **Features orphelines** : Présentes dans la DB mais absentes du code (routes/vues supprimées)
2. **Features manquantes** : Présentes dans le code mais absentes de la DB

**Exemple de sortie :**
```
🔍 Checking features consistency...

⚠️  Orphaned Features (in DB but not in code):
+-------------------+
| Slug              |
+-------------------+
| old-route.index   |
+-------------------+
Run with --fix to automatically remove these features.

⚠️  Missing Features (in code but not in DB):
+-------------------+
| Slug              |
+-------------------+
| new-route.create  |
+-------------------+
Run "php artisan features:scan" to register these features.

📊 Summary:
   Orphaned: 1
   Missing: 1
```

---

## ⚙️ permissions:generate

**Description :** Génère automatiquement les permissions CRUD par défaut pour un ou plusieurs modules.

**Usage :**
```bash
php artisan permissions:generate users           # Pour un module spécifique
php artisan permissions:generate --all           # Pour tous les modules
```

**Arguments :**
- `module` : Slug du module (optionnel si --all est utilisé)

**Options :**
- `--all` : Génère les permissions pour tous les modules

**Permissions générées :**
Pour chaque module, crée 4 permissions :
- `{module}.view` - Voir les éléments du module
- `{module}.create` - Créer de nouveaux éléments
- `{module}.edit` - Modifier les éléments existants
- `{module}.delete` - Supprimer les éléments

Chaque permission est automatiquement liée aux features correspondantes du module.

**Exemple de sortie :**
```
🔧 Generating permissions for module: Users

Processing: Gestion Utilisateurs
  ✓ View Gestion Utilisateurs (users.view)
  ✓ Create Gestion Utilisateurs (users.create)
  ✓ Edit Gestion Utilisateurs (users.edit)
  ✓ Delete Gestion Utilisateurs (users.delete)

✅ Successfully generated 4 permissions!
```

---

## 🎨 make:module

**Description :** Crée rapidement un nouveau module avec sa configuration par défaut.

**Usage :**
```bash
php artisan make:module "Gestion Stock"
php artisan make:module "Rapports" --icon=bx-chart --color=#D4AF37
php artisan make:module "Admin Panel" --core
```

**Arguments :**
- `name` : Nom du module (requis)

**Options :**
- `--icon=<class>` : Classe d'icône Boxicons (défaut: `bx-cube`)
- `--color=<hex>` : Code couleur hexadécimal (défaut: `#B8860B`)
- `--core` : Marque le module comme "core" (non supprimable)

**Workflow interactif :**
1. Crée le module avec les paramètres fournis
2. Propose de générer les permissions CRUD automatiquement
3. Affiche les prochaines étapes suggérées

**Exemple de sortie :**
```
🔧 Creating module: Gestion Stock

✓ Module created successfully!
+----------+-------------------+
| Property | Value             |
+----------+-------------------+
| Name     | Gestion Stock     |
| Slug     | gestion-stock     |
| Icon     | bx-package        |
| Color    | #B8860B           |
| Order    | 14                |
| Core     | No                |
+----------+-------------------+

Generate default CRUD permissions for this module? (yes/no) [yes]:
> yes

🔧 Generating permissions for module: gestion-stock
  ✓ View Gestion Stock (gestion-stock.view)
  ✓ Create Gestion Stock (gestion-stock.create)
  ✓ Edit Gestion Stock (gestion-stock.edit)
  ✓ Delete Gestion Stock (gestion-stock.delete)

📝 Next steps:
  1. Run "php artisan features:scan" to detect features for this module
  2. Assign permissions to roles via the admin interface
  3. Use feature components in your Blade views
```

---

## 📋 Workflow Recommandé

### Nouveau Module
```bash
# 1. Créer le module
php artisan make:module "Mon Module" --icon=bx-star --color=#D4AF37

# 2. Créer vos routes et vues avec les composants feature

# 3. Scanner les features
php artisan features:scan

# 4. Assigner les permissions via l'interface admin
# Aller sur /admin/roles/{id}/permissions
```

### Maintenance Régulière
```bash
# Vérifier la cohérence
php artisan features:check

# Si des features orphelines existent
php artisan features:check --fix

# Si des features manquantes sont détectées
php artisan features:scan
```

### Déploiement
```bash
# 1. Migrer la base de données
php artisan migrate

# 2. Scanner toutes les features
php artisan features:scan

# 3. Vérifier la cohérence
php artisan features:check
```

---

## 🎯 Exemples d'Utilisation

### Ajouter une nouvelle fonctionnalité
```blade
<!-- Dans votre vue Blade -->
<x-feature-button feature="users.export" class="btn btn-success">
    <i class="bx bx-download"></i> {{ __('Export') }}
</x-feature-button>
```

```bash
# Scanner la nouvelle feature
php artisan features:scan
```

### Refactoriser des routes
```bash
# Avant de supprimer des routes, vérifier l'impact
php artisan features:check

# Après suppression, nettoyer la DB
php artisan features:check --fix
```

### Configuration initiale d'un nouvel environnement
```bash
# 1. Seeds des modules de base
php artisan db:seed --class=ModulesSeeder

# 2. Générer toutes les permissions
php artisan permissions:generate --all

# 3. Scanner toutes les features
php artisan features:scan

# 4. Configurer les rôles via l'interface admin
```
