# Agent Configuration Directory

Ce répertoire `.agent` contient les fichiers de configuration et de contexte pour les agents IA travaillant sur ce projet.

## Fichiers Importants

### 📘 PROJECT_CONTEXT.md
**Le fichier le plus important !** Contient le contexte global du projet que TOUS les agents doivent lire au début de chaque conversation.

#### Contenu clé :
- Nature hybride E-commerce + Fintech
- Architecture et technologies
- Règles de design et UX
- Règles métier importantes
- Décisions de design récentes

#### 🚨 À lire OBLIGATOIREMENT par les agents avant toute intervention !

### 📁 workflows/ (si existant)
Workflows personnalisés pour automatiser certaines tâches récurrentes.

---

## Utilisation pour les Agents IA

Quand un agent commence à travailler sur ce projet, il devrait :

1. ✅ Lire `PROJECT_CONTEXT.md` en entier
2. ✅ Comprendre la nature **hybride** e-commerce/fintech
3. ✅ Respecter les règles de design définies
4. ✅ Suivre les principes UX établis
5. ✅ Consulter les décisions récentes

---

## Mise à Jour du Contexte

Le fichier `PROJECT_CONTEXT.md` doit être mis à jour quand :

- ❗ Une décision architecturale majeure est prise
- ❗ De nouvelles règles métier sont établies
- ❗ Le design system évolue significativement
- ❗ De nouveaux modules importants sont ajoutés
- ❗ Des conventions de code sont définies

---

## Structure Recommandée

```
.agent/
├── README.md                    # Ce fichier
├── PROJECT_CONTEXT.md           # ⭐ Contexte permanent du projet
└── workflows/                   # Workflows automatisés (optionnel)
    ├── deploy.md
    └── test.md
```

---

**Note**: Ce répertoire peut être versionné avec Git pour partager le contexte avec toute l'équipe (humains et IA).
