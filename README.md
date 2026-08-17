```markdown
# StoreManager Pro

Un ERP (Enterprise Resource Planning) de gestion de boutique conçu pour les commerces au Sénégal.

## Description

StoreManager Pro est une application web de gestion commerciale qui permet de piloter l'ensemble des opérations d'une boutique ou d'un commerce. Elle regroupe plusieurs modules essentiels pour une gestion efficace.

## Fonctionnalités

### Modules disponibles

- **Tableau de Bord** : Visualisation des indicateurs clés (ventes, dettes, stock, approvisionnements)
- **Gestion des Ventes (POS)** : Interface tactile pour enregistrer les ventes, gérer le panier et les paiements
- **Gestion des Dettes** : Suivi des créances clients, remboursements partiels ou totaux
- **Gestion des Approvisionnements** : Suivi des bons de livraison et réception de marchandises
- **Gestion des Produits & Tiers** : Catalogue produits, clients et fournisseurs

### Gestion des rôles

Quatre profils d'utilisateurs avec des permissions différentes :
- **Admin Boutique** : Accès complet à tous les modules
- **Chargé de Vente** : Accès à la caisse POS et à la gestion des dettes
- **Chargé de Stock** : Accès aux approvisionnements et au catalogue
- **Inventaire** : Consultation des produits et stocks

## Prérequis

- PHP 8.0 ou supérieur
- PostgreSQL ou SQLite
- Serveur web (Apache, Nginx)

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/RouguiSy/StoreManager.git
cd StoreManager
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer la base de données

#### PostgreSQL

```bash
psql -U postgres -f database/schema.sql
```

#### SQLite

```bash
sqlite3 erp.db < database/schema_sqlite.sql
```

### 4. Configurer les identifiants de connexion

Créer un fichier `config/database.php` :

```php
<?php
return [
    'host' => 'localhost',
    'port' => '5432',
    'dbname' => 'storemanager',
    'user' => 'postgres',
    'password' => 'votre_mot_de_passe'
];
```

### 5. Lancer l'application

```bash
php -S localhost:8000
```

## Structure du projet

```
StoreManager/
├── config/                     # Fichiers de configuration
│   └── database.php            # Configuration de la base de données
├── database/                   # Scripts SQL
│   ├── schema.sql              # Schéma PostgreSQL
│   └── schema_sqlite.sql       # Schéma SQLite
├── docs/                       # Documentation
│   ├── diagrams/               # Diagrammes UML
│   │   ├── Classe.puml         # Diagramme de classes
│   │   ├── UseCaseAdmin.puml   # Cas d'utilisation Admin
│   │   ├── UseCaseVente.puml   # Cas d'utilisation Vente
│   │   ├── useCaseStock.puml   # Cas d'utilisation Stock
│   │   └── UseCaseInventaire.puml # Cas d'utilisation Inventaire
│   └── img/                    # Images des diagrammes
├── src/
│   ├── Core/                   # Noyau de l'application
│   │   ├── Database.php        # Singleton avec fallback PostgreSQL/SQLite
│   │   └── SessionManager.php  # Gestion des sessions
│   ├── Model/
│   │   ├── Entity/             # Entités POO
│   │   │   ├── Role.php
│   │   │   ├── Utilisateur.php
│   │   │   ├── Client.php
│   │   │   ├── Fournisseur.php
│   │   │   ├── Produit.php
│   │   │   ├── ModePaiement.php
│   │   │   ├── Vente.php
│   │   │   ├── LigneVente.php
│   │   │   ├── Dette.php
│   │   │   ├── Paiement.php
│   │   │   ├── Approvisionnement.php
│   │   │   └── LigneApprovisionnement.php
│   │   └── Repository/         # Repositories (accès aux données)
│   │       ├── ClientRepository.php
│   │       ├── FournisseurRepository.php
│   │       ├── ProduitRepository.php
│   │       └── DetteRepository.php
│   ├── Service/                # Services métier
│   │   ├── VenteService.php    # Gestion des ventes
│   │   └── DebtService.php     # Gestion des dettes
│   └── Controller/             # Contrôleurs
│       └── POSController.php   # Contrôleur de la caisse
├── views/                      # Vues
│   ├── pos/
│   │   └── index.php           # Interface caisse POS
│   └── dettes/
│       └── index.php           # Gestion des dettes
├── DEVLOG.md                   # Journal de développement
└── README.md                   # Ce fichier
```

## Technologies utilisées

- **PHP** (POO, PDO)
- **PostgreSQL** / **SQLite** (Base de données avec fallback automatique)
- **HTML/CSS** (Interface utilisateur)
- **JavaScript** (Interactions dynamiques)
- **UML** (Conception)

## Fonctionnalités principales

### Vente POS
- Sélection d'un client
- Ajout de produits au panier
- Gestion du stock (décrémentation automatique)
- Gestion du crédit client
- Création automatique de dette en cas de paiement partiel
- Génération de facture

### Gestion des Dettes
- Liste des dettes actives
- Remboursement partiel ou total
- Historique des paiements
- Mise à jour automatique du statut (SOLDEE/NON_SOLDEE)

### Approvisionnements
- Réception de bons de livraison
- Incrémentation automatique du stock
- Suivi des commandes en cours

## Architecture

L'application suit une architecture en couches :

1. **Base de données** : PostgreSQL ou SQLite
2. **Core** : Connexion à la base et gestion des sessions
3. **Model/Entity** : Entités métier (POO)
4. **Model/Repository** : Accès aux données (requêtes SQL préparées)
5. **Service** : Logique métier
6. **Controller** : Gestion des requêtes
7. **Views** : Affichage

## Base de données (Fallback automatique)

L'application tente d'abord de se connecter à PostgreSQL. Si la connexion échoue, elle bascule automatiquement sur SQLite (`erp.db`). L'utilisateur ne voit aucune différence.

## Sécurité

- Toutes les requêtes SQL utilisent des **requêtes préparées PDO** (protection contre les injections SQL)
- Les mots de passe sont hashés avec `password_hash()`
- Les sessions sont gérées de manière centralisée

## Auteur

**Rougui Sy**


Ce projet est réalisé dans le cadre d'un projet de formation.
