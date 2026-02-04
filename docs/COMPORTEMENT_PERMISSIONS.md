# Comportement du Système de Permissions

## Mode Permissif par Défaut ✅

Le système de permissions fonctionne en **mode permissif** par défaut. Cela signifie :

### Règles d'Accès

**Scénario 1 : Feature NON assignée à une permission**
- ✅ **Visible et accessible** par tous les utilisateurs authentifiés
- Permet une implémentation progressive du système
- Utile en phase de développement

**Scénario 2 : Feature assignée à une ou plusieurs permissions**
- 🔒 **Contrôlée** : Visible uniquement si l'utilisateur a au moins une des permissions
- Si l'utilisateur n'a pas la permission → élément caché ou désactivé

### Logique d'Implémentation

```php
// Dans les composants feature-button, feature-link, feature-section
if (!$featureExists) {
    // Feature pas encore assignée → AUTORISER (permissif)
    $canAccess = true;
} else {
    // Feature assignée → VÉRIFIER les permissions
    $canAccess = $user->roles()
        ->whereHas('permissions.features', ...)
        ->exists();
}
```

### Avantages

1. **Développement incrémental** : Ajouter des features sans bloquer immédiatement
2. **Rétro-compatibilité** : Les anciennes features restent accessibles
3. **Flexibilité** : Définir les restrictions progressivement

### Mise en Production

Pour renforcer la sécurité en production :

1. **Scanner toutes les features** :
   ```bash
   php artisan features:scan
   ```

2. **Générer les permissions** :
   ```bash
   php artisan permissions:generate --all
   ```

3. **Assigner aux rôles** via l'interface admin `/admin/roles/{id}/permissions`

4. **Vérifier qu'aucune feature n'est orpheline** :
   ```bash
   php artisan features:check
   ```

### Mode Strict (Future Amélioration)

Pour passer en mode strict plus tard, on pourra ajouter :

**Option 1 : Configuration globale**
```php
// config/permissions.php
'strict_mode' => env('PERMISSIONS_STRICT_MODE', false),
```

**Option 2 : Par composant**
```blade
<x-feature-button feature="users.delete" strict="true">
    Delete
</x-feature-button>
```

En mode strict :
- Feature non assignée → **REFUSER** l'accès
- Plus sécurisé mais nécessite une configuration complète
