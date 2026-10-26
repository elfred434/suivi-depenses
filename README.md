# Suivi de dépenses

Application Laravel de suivi personnel des dépenses, catégories et montants en FCFA.

## Prérequis

- PHP 8.3 ou supérieur
- Composer
- SQLite (ou un moteur de base de données pris en charge par Laravel)
- Node.js et npm pour compiler les ressources front-end

## Installation

Depuis la racine du projet :

```bash
composer install
```

Copiez `.env.example` dans `.env` (PowerShell sous Windows) :

```powershell
Copy-Item .env.example .env
if (!(Test-Path database\database.sqlite)) {
    New-Item -ItemType File database\database.sqlite
}
php artisan key:generate
```

Configurez ensuite la base de données dans `.env`, puis lancez les migrations et installez les dépendances front-end :

```bash
php artisan migrate
npm install
npm run build
```

## Lancement

```bash
php artisan serve
```

Ouvrez l’URL indiquée par Artisan. L’inscription permet de créer un compte ; la gestion des catégories et dépenses requiert une connexion.

## Données de démonstration

```bash
php artisan db:seed
```

Le seeder ajoute des catégories et dépenses sans créer de doublons. Il utilise un compte de démonstration explicite (`demo@example.test`) : s’il n’existe pas, il le crée avec un mot de passe aléatoire affiché lors de la création. Conservez ce mot de passe uniquement pour votre environnement local ; ne lancez pas le seeder de démonstration en production.

## Tests

Les tests fonctionnels utilisent SQLite en mémoire et ne modifient pas la base de travail :

```bash
php artisan test
```

Ou avec le script Composer :

```bash
composer test
```

## Fonctionnalités

- Inscription, connexion et déconnexion.
- Catégories et dépenses isolées par utilisateur.
- Dépenses en FCFA entiers, avec filtres cumulables par libellé, catégorie et dates.
- Liste triée par date et identifiant décroissants, paginée par 10.
- Total des dépenses calculé sur l’ensemble des résultats filtrés.
