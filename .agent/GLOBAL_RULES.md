# RÈGLES GLOBALES POUR TOUS LES AGENTS

> **CRITIQUES** : Ces règles doivent être suivies par TOUS les agents, TOUJOURS.

---

## 1. ARCHITECTURE & CODE

### 1.1 Service Layer OBLIGATOIRE
- ✅ Utiliser des Services pour la logique métier
- ❌ NE PAS mettre de logique métier dans les contrôleurs
- ✅ Pour toute route Web, créer également une route API équivalente
- ✅ Controllers Web et API partagent les mêmes Services

### 1.2 Gates Laravel pour Permissions
- ✅ Utiliser `Gate::define()` dans AppServiceProvider
- ✅ Protéger les routes avec `middleware('can:gate-name')`
- ❌ NE PAS créer de méthodes `checkPermission()` dans les contrôleurs
- ❌ NE PAS utiliser de middleware personnalisé si un Gate peut faire le travail

### 1.3 Profils Utilisateurs
- ✅ Utiliser `profil_id` (relation à la table `profils`)
- ❌ NE PAS utiliser le champ `role` (obsolète, supprimé)
- ✅ Utiliser `$user->isSuperAdmin()` pour vérifier les permissions
- ✅ Utiliser `$user->hasRole('role-slug')` pour rôles granulaires

---

## 2. INTERNATIONALISATION (i18n)

### 2.1 Langues Supportées
- ✅ Français (fr) et Anglais (en) UNIQUEMENT
- ❌ NE PAS assumer d'autres langues sans confirmation explicite
- ✅ Langue par défaut : Français
- ✅ Fichiers : `lang/en.json`, `lang/fr.json`

### 2.2 Utilisation du Helper `__()`
- ✅ TOUJOURS utiliser `__()` pour les textes affichés à l'utilisateur
- ✅ Clés en anglais : `__('Profile Management')` et non `__('gestion_profil')`
- ✅ Clés descriptives : `__('Profile created successfully.')` plutôt que `__('Success')`
- ❌ NE PAS mélanger du texte en dur et des traductions

**Exemples** :
```blade
{{-- Vue --}}
{{ __('Dashboard') }}

{{-- Section title --}}
@section('title', __('Profile Management'))

{{-- JavaScript --}}
title: '{{ __('Are you sure?') }}'
```

```php
// Contrôleur
return redirect()->with('success', __('Profile created successfully.'));
abort(403, __('Access denied...'));
```

### 2.3 DataTables Localisation
- ✅ Utiliser des fichiers locaux : `public/assets/vendor/libs/datatables-bs5/i18n/fr-FR.json`
- ❌ NE JAMAIS utiliser de CDN pour les traductions DataTables

---

## 3. DESIGN & UX

### 3.1 Couleurs du Logo
- ✅ TOUJOURS respecter la palette or/gris du logo (`assets/img/branding/logo.png`)
- Brand Gold: `#B8860B`, Brand Gold Light: `#D4AF37`
- Brand Grey: `#808080`, Brand Grey Dark: `#505050`
- ❌ NE PAS utiliser de couleurs aléatoires

### 3.2 Design Moderne
- ✅ Utiliser des gradients, glassmorphisme, animations fluides
- ✅ Border-radius arrondis (20px cartes, 50px boutons)
- ✅ Design responsive (mobile-first)
- ❌ NE JAMAIS créer d'UI basique ou "MVP minimal"

---

## 4. NATURE HYBRIDE E-COMMERCE + FINTECH

### 4.1 Wallet-First
- ✅ Toutes les transactions passent par le wallet
- ❌ PAS de paiement direct (sauf cash on delivery)
- ✅ KYC vérifié requis pour certaines actions (devenir vendeur)

### 4.2 Navigation Produits
- ✅ Tout le monde peut consulter les produits SANS connexion
- ✅ Connexion requise UNIQUEMENT pour ajouter au panier / acheter
- ✅ Afficher clairement cette restriction aux visiteurs

---

## 5. DOCUMENTATION

### 5.1 Rapport Technique
- ✅ Mettre à jour `document/RAPPORT_TECHNIQUE_2026.md` après CHAQUE tâche complétée
- ✅ Documenter: objectif, fichiers modifiés, logique implémentée, workflow

### 5.2 PROJECT_CONTEXT.md
- ✅ Consulter `.agent/PROJECT_CONTEXT.md` au début de chaque conversation
- ✅ Mettre à jour si nouvelles fonctionnalités majeures

---

## 6. ANTI-HALLUCINATIONS CRITIQUES

### 6.1 Ce qu'il NE FAUT JAMAIS FAIRE

1. ❌ **Langues** : Assumer d'autres langues que FR et EN
2. ❌ **CDN** : Utiliser des CDN pour DataTables i18n (fichiers locaux uniquement)
3. ❌ **Permissions** : Créer des middleware/méthodes personnalisées si un Gate existe
4. ❌ **Logique Métier** : Mettre la logique dans les Controllers au lieu des Services
5. ❌ **Profils** : Utiliser le champ `role` (obsolète, remplacé par `profil_id`)
6. ❌ **Traductions** : Mélanger texte en dur et `__()`
7. ❌ **Tests** : Utiliser des données fictives si de vraies données existent
8. ❌ **API** : Oublier de créer les routes API quand on crée des routes Web
9. ❌ **DataTables CDN** : `https://cdn.datatables.net/plug-ins/.../i18n/fr-FR.json` (interdit!)
10. ❌ **Middleware Custom** : Créer `CheckSuperAdmin` si `Gate::define('super-admin-access')` existe

### 6.2 Ce qu'il FAUT TOUJOURS FAIRE

1. ✅ **`__()`** : Pour TOUT texte affiché à l'utilisateur
2. ✅ **Gates** : `middleware('can:gate-name')` pour les permissions
3. ✅ **Services** : Logique métier centralisée et réutilisable
4. ✅ **Web + API** : Routes des deux côtés, Services partagés
5. ✅ **`profil_id`** : Utiliser la table `profils`, pas le champ `role`
6. ✅ **Logo Colors** : Respecter la palette or/gris officielle
7. ✅ **Rapport Technique** : Documenter après chaque tâche
8. ✅ **Feature Protection** : Protéger toutes les pages sensibles avec `<x-feature-*>`
9. ✅ **Features Scan** : Lancer `php artisan features:scan` après modifications

---

## 7. SYSTÈME DE PROTECTION PAR FEATURES

### 7.1 Protection Obligatoire des Pages

> [!IMPORTANT]
> **TOUTES les pages sensibles doivent être protégées** avec les composants `<x-feature-*>`.

#### Composants à Utiliser

1. **`<x-feature-section>`** : Protection de blocs entiers
   ```blade
   <x-feature-section feature="admin.roles.view-list" showDeniedMessage="true">
       <table>...</table>
   </x-feature-section>
   ```

2. **`<x-feature-link>`** : Protection de liens
   ```blade
   <x-feature-link feature="admin.roles.edit" route="{{route(...)}}">
       Edit
   </x-feature-link>
   ```

3. **`<x-feature-button>`** : Protection de boutons
   ```blade
   <x-feature-button feature="admin.roles.delete" type="submit">
       Delete
   </x-feature-button>
   ```

### 7.2 Nomenclature des Features

**Format** : `<module>.<entity>.<action>`

**Exemples** :
- Routes: `admin.roles.create`, `admin.roles.edit`
- UI: `admin.roles.view-list`, `admin.roles.form-fields`
- Form Parts: `admin.modules.form-basic-info`, `admin.modules.form-settings`

**Conventions** :
- Modules: `admin`, `seller`, `user`, `wallet`
- Actions CRUD: `index`, `create`, `store`, `edit`, `update`, `destroy`
- Actions UI: `view-list`, `view-details`, `manage-X`, `assign-X`
- Form Parts: `form-fields`, `form-basic-info`, `form-settings`

### 7.3 Patterns de Protection

#### Page Index
```blade
<!-- Bouton Create -->
<x-feature-link feature="admin.X.create" route="..." class="btn btn-primary">
    Add New
</x-feature-link>

<!-- Table protégée -->
<x-feature-section feature="admin.X.view-list" showDeniedMessage="true">
    <table>...</table>
</x-feature-section>

<!-- Actions -->
<x-feature-link feature="admin.X.edit" route="...">Edit</x-feature-link>
<x-feature-button feature="admin.X.delete" type="submit">Delete</x-feature-button>
```

#### Page Create/Edit
```blade
<form>
    <x-feature-section feature="admin.X.form-basic-info">
        <!-- Champs groupe 1 -->
    </x-feature-section>
    
    <x-feature-section feature="admin.X.form-settings">
        <!-- Champs groupe 2 -->
    </x-feature-section>
    
    <!-- Submit SANS protection (règle importante) -->
    <button type="submit">Save</button>
</form>
```

### 7.4 Règles Strictes

1. ✅ **Tables** : TOUJOURS protéger avec `<x-feature-section>` + `showDeniedMessage="true"`
2. ✅ **Boutons Create/Edit/Delete** : TOUJOURS protéger avec `<x-feature-link>` ou `<x-feature-button>`
3. ✅ **Formulaires** : Grouper logiquement et protéger chaque groupe
4. ❌ **Boutons Submit** : NE JAMAIS protéger les boutons "Save"/"Update"/"Submit"
5. ✅ **Routes nommées** : Toujours nommer les routes (auto-détection par scan)
6. ✅ **Scan après modification** : Lancer `php artisan features:scan` après chaque changement

### 7.5 Workflow Développement

#### Nouvelle Page
```
1. Créer la Blade view
2. Protéger avec <x-feature-*>
3. Créer la route nommée
4. php artisan features:scan
5. Configurer permissions dans admin
6. Tester
```

#### Nouvelle Route
```
1. Ajouter route avec nom dans web.php
2. php artisan features:scan
3. Feature créée automatiquement
4. Assigner à une permission
5. Tester
```

---

## 8. CHECKLIST AVANT COMMIT

Avant chaque modification, vérifier :

- [ ] Logique métier dans un Service (pas dans Controller)
- [ ] Routes Web ET API créées (si applicable)
- [ ] Permissions gérées par Gates (`can:`)
- [ ] Textes UI traduits avec `__()`
- [ ] Couleurs du logo respectées
- [ ] `profil_id` utilisé (pas `role`)
- [ ] Documentation mise à jour (rapport technique)
- [ ] DataTables localisé (fichiers locaux)
- [ ] **Pages protégées avec `<x-feature-*>`**
- [ ] **`php artisan features:scan` exécuté**

---

## 8. WORKFLOW STANDARD

### Nouvelle Fonctionnalité
```
1. Lire PROJECT_CONTEXT.md
2. Créer Service (logique métier)
3. Créer Controllers Web ET Api
4. Créer Routes dans web.php et api.php
5. Créer Views (avec `__()` partout)
6. Ajouter Gates si nécessaire
7. Tester
8. Mettre à jour RAPPORT_TECHNIQUE_2026.md
```

### Nouvelle Page
```
1. Design avec couleurs du logo
2. Utiliser `__()` pour tous les textes
3. Vérifier permissions (Gates)
4. Responsive mobile-first
5. Documenter
```

---

**Dernière mise à jour** : 2026-02-03  
**Version** : 1.0  
**Application aux agents** : TOUS (Antigravity, futurs agents)
