
# 📓 Journal de Développement (DEVLOG)

**Nom & Prénom** : Rougui Sy
**Projet** : StoreManager Pro (ERP PHP/POO)

---

## 1. Suivi Chronologique des Phases

### 🌃 [Vendredi - Phase 1] : Conception & BDD Fallback

#### 📌 Step 1.1 (19h00 - 20h30) : Conception UML

- **Heure de réalisation** : 19h00 - 20h30
- **Ce qui a été fait** :

  - Création du dossier `/docs/diagrams` à la racine du projet.
  - Définition des 4 acteurs : `Admin`, `Chargé de Vente`, `Chargé de Stock`, `Inventaire`.
  - Rédaction du diagramme de cas d'utilisation (Use Case) pour l'acteur **Admin** :
    - Cas principaux : Consulter Tableau de Bord, Gérer Ventes (POS), Gérer Dettes, Gérer Approvisionnements, Gérer Produits & Tiers.
    - Cas secondaires identifiés : Voir ventes récentes, Voir dettes du jour, Voir ruptures de stock, Voir livraisons du jour, Voir clients débiteurs, S'approvisionner, Valider vente, Voir registre général, Voir registre actif, Rembourser dette, Lister livraisons, Ajouter client, Ajouter fournisseur, Ajouter produit, Voir ligne, Réceptionner, Voir valeur totale du stock, Voir articles au catalogue, Voir clients enregistrés.
  - Rédaction du diagramme de cas d'utilisation pour l'acteur **Chargé de Vente** :
    - Cas principaux : Consulter Tableau de Bord, Gérer Ventes (POS), Gérer Dettes.
    - Cas secondaires : Voir ventes récentes, Voir dettes du jour, Voir clients débiteurs, Valider vente, Voir registre général, Voir registre actif, Rembourser dette.
  - Rédaction du diagramme de cas d'utilisation pour l'acteur **Chargé de Stock** :
    - Cas principaux : Consulter Tableau de Bord, Gérer Approvisionnements, Gérer Produits & Tiers.
    - Cas secondaires : Voir ruptures de stock, Voir livraisons du jour, S'approvisionner, Lister livraisons, Voir ligne, Réceptionner, Ajouter produit, Ajouter fournisseur.
  - Rédaction du diagramme de cas d'utilisation pour l'acteur **Inventaire** :
    - Cas principaux : Consulter Tableau de Bord, Gérer Produits & Tiers.
    - Cas secondaires : Voir ruptures de stock, Voir valeur totale du stock, Voir articles au catalogue, Voir clients enregistrés.
  - Rédaction du diagramme de classes UML complet avec 11 entités :
    - `Role` : Gestion des rôles et permissions
    - `Utilisateur` : Gestion des utilisateurs avec authentification
    - `Client` : Gestion des clients avec limite de crédit
    - `Fournisseur` : Gestion des fournisseurs
    - `Produit` : Gestion du stock avec seuil d'alerte
    - `Vente` et `LigneVente` : Gestion des transactions POS
    - `Dette` et `Paiement` : Gestion des crédits clients
    - `ModePaiement` : Types de paiement (Wave, OM, Espèces)
    - `Approvisionnement` et `LigneApprovisionnement` : Gestion des réceptions BL
  - Définition des relations entre les entités avec les cardinalités.
  - Définition des contraintes métier (statuts, seuils d'alerte).
- **Difficultés / Obstacles** :

  - La modélisation de la gestion des rôles a été simplifiée en utilisant une classe `Role` associée à `Utilisateur` plutôt qu'une hiérarchie d'héritage complexe.
  - La relation entre `Vente` et `Dette` a été clarifiée : une vente peut générer une dette si le paiement est partiel.
  - L'identification des cas d'utilisation secondaires s'est limitée aux fonctionnalités réellement présentes dans l'interface HTML.

#### 📌 Step 1.2 (20h30 - 22h00) : Schéma SQL PostgreSQL / SQLite

- **Heure de réalisation** : 20h30 - 22h00
- **Ce qui a été fait** :

  - Création du dossier `/database` à la racine du projet.
  - Création du script SQL pour PostgreSQL (`database/schema.sql`) avec :
    - Toutes les tables correspondant au diagramme de classes : roles, utilisateurs, clients, fournisseurs, produits, modes_paiement, ventes, lignes_vente, dettes, paiements, approvisionnements, lignes_approvisionnement
    - Contraintes d'intégrité (clés primaires, clés étrangères)
    - Contraintes CHECK pour valider les statuts (EN_ATTENTE, PAYEE, ANNULEE, PARTIELLE, SOLDEE, NON_SOLDEE, EN_RETARD, RECU)
    - Contraintes CHECK pour valider les montants (>= 0)
    - Index sur les clés étrangères pour optimiser les performances
    - Index sur les champs de recherche fréquents (statut, ref, reference_bl)
    - Insertion des données de base (rôles, modes de paiement)
    - Insertion d'un utilisateur admin par défaut (email: admin@storemanager.sn)
    - Vues utiles : v_dettes_clients et v_stock_produits
  - Création du script SQL pour SQLite (`database/schema_sqlite.sql`) avec :
    - Adaptation de la syntaxe (AUTOINCREMENT vs SERIAL)
    - Adaptation des types de données (REAL vs DECIMAL, DATETIME vs TIMESTAMP)
    - Activation des clés étrangères avec `PRAGMA foreign_keys = ON`
    - Mêmes contraintes et données de base que PostgreSQL
    - Mêmes vues que PostgreSQL
  - Ajout de la contrainte UNIQUE sur `dettes.vente_id` pour garantir qu'une vente ne peut avoir qu'une seule dette
- **Difficultés / Obstacles** :

  - Gestion des différences de syntaxe entre PostgreSQL et SQLite :
    - `SERIAL` vs `INTEGER PRIMARY KEY AUTOINCREMENT`
    - `TIMESTAMP` vs `DATETIME`
    - `DECIMAL(15,2)` vs `REAL`
  - Activation obligatoire des clés étrangères dans SQLite avec `PRAGMA foreign_keys = ON`

#### 📌 Step 1.3 (22h00 - 23h00) : Singleton Database & Fallback Automatique

- **Heure de réalisation** : 22h00 - 23h00
- **Ce qui a été fait** :

  - Création du dossier `src/Core/`.
  - Création de la classe `src/Core/Database.php`.
  - Implémentation du **pattern Singleton** avec une méthode statique `connexionDB()` pour garantir une connexion unique à la base de données.
  - Mise en place du **mécanisme de fallback** :
    1. Tentative de connexion à PostgreSQL avec les paramètres définis en dur (user, password, host, port, dbname).
    2. En cas d'échec (exception PDO), bascule automatique sur SQLite (`erp.db` à la racine du projet).
    3. Le fichier SQLite est créé automatiquement s'il n'existe pas.
  - Utilisation de `dirname(__DIR__, 2)` pour remonter proprement à la racine du projet.
  - Activation automatique des clés étrangères pour SQLite (`PRAGMA foreign_keys = ON`).
  - Initialisation automatique du schéma SQLite à partir du fichier `database/schema_sqlite.sql`.
  - Méthodes utilitaires :
    - `query()` : Exécuter une requête SELECT simple
    - `prepare()` : Préparer et exécuter une requête avec paramètres
    - `executeQuery()` : Exécuter une requête SELECT avec paramètres
    - `executeUpdate()` : Exécuter une requête INSERT/UPDATE/DELETE
    - `getAllTable()` : Récupérer toutes les données d'une table
    - `beginTransaction()`, `commit()`, `rollBack()` : Gestion des transactions
  - Les erreurs sont capturées pour éviter d'afficher des informations sensibles à l'utilisateur.
- **Difficultés / Obstacles** :

  - Gestion propre des exceptions PDO sans exposer les détails de la base de données.
  - Vérification des droits d'écriture pour le fichier SQLite.
  - Construction des chemins avec `dirname(__DIR__, 2)` pour garantir la portabilité du projet.

---

### ☀️ [Samedi - Phase 2] : POO, Repositories & Ventes POS

#### 📌 Step 2.1 (09h00 - 11h00) : Entités POO Pure

- **Heure de réalisation** : 09h00 - 11h00
- **Ce qui a été fait** :

  - Création du dossier `src/Model/Entity/`.
  - Création de toutes les entités POO :
    - `Role.php` : Gestion des rôles
    - `Utilisateur.php` : Gestion des utilisateurs avec authentification
    - `Client.php` : Gestion des clients avec limite de crédit
    - `Fournisseur.php` : Gestion des fournisseurs
    - `Produit.php` : Gestion du stock avec seuil d'alerte
    - `ModePaiement.php` : Types de paiement (Wave, OM, Espèces)
    - `Vente.php` et `LigneVente.php` : Gestion des transactions POS
    - `Dette.php` et `Paiement.php` : Gestion des crédits clients
    - `Approvisionnement.php` et `LigneApprovisionnement.php` : Gestion des réceptions BL
  - Implémentation des relations entre entités par **objets** (comme demandé par le professeur) :
    - `Utilisateur` → `Role` (objet Role)
    - `Vente` → `Client` et `Utilisateur` (objets)
    - `LigneVente` → `Produit` et `Vente` (objets)
    - `Dette` → `Client` et `Vente` (objets)
    - `Paiement` → `Dette`, `Utilisateur` et `ModePaiement` (objets)
    - `Approvisionnement` → `Fournisseur` et `Utilisateur` (objets)
    - `Produit` → `Fournisseur` (objet)
  - Ajout des méthodes métier :
    - `verifyPassword()` pour l'authentification
    - `peutAcheter()` et `updateSolde()` pour les clients
    - `updateStock()` et `isStockLow()` pour les produits
    - `updateStatut()` pour les ventes et dettes
    - `applyPayment()` pour les remboursements
    - `getMontantRestant()` pour les calculs de reste à payer
- **Difficultés / Obstacles** :

  - Comprendre la différence entre l'ID et l'objet pour les relations
  - Bien gérer l'encapsulation avec des getters et setters
  - Implémenter les méthodes métier correctement

#### 📌 Step 2.2 (11h00 - 13h00) : Repositories & SQL Sécurisé

- **Heure de réalisation** : 11h00 - 13h00
- **Ce qui a été fait** :

  - Création du dossier `src/Model/Repository/`.
  - Création de `ClientRepository.php` avec :
    - `insert()` : Insertion d'un client
    - `selectById()` : Recherche par ID
    - `selectByTelephone()` : Recherche par téléphone
    - `selectAll()` : Récupération de tous les clients
    - `selectDebiteurs()` : Clients avec dettes actives
    - `getSoldeDisponible()` : Calcul du solde disponible
    - `update()` : Mise à jour d'un client
    - `updateSolde()` : Mise à jour du solde
    - `delete()` : Suppression
    - `toObjet()` : Mapping SQL → Objet
  - Création de `FournisseurRepository.php` avec :
    - `insert()`, `selectById()`, `selectByNom()`, `selectAll()`
    - `update()`, `delete()`, `toObjet()`
  - Création de `ProduitRepository.php` avec :
    - `insert()`, `selectById()`, `selectByCode()`, `selectAll()`
    - `selectLowStock()` : Produits en dessous du seuil d'alerte
    - `selectByCategorie()` : Filtrage par catégorie
    - `selectByFournisseur()` : Filtrage par fournisseur
    - `update()`, `updateStock()`, `delete()`, `toObjet()`
  - Toutes les requêtes utilisent des **requêtes préparées PDO** avec paramètres nommés.
  - Sécurisation complète contre les injections SQL.
- **Difficultés / Obstacles** :

  - Mise en place du mapping SQL → Objet avec `toObjet()`
  - Gestion des requêtes préparées avec paramètres nommés
  - Respect du pattern Repository

---

## 2. Autopsie de 3 Méthodes Clés (Indispensable pour l'oral)

### Méthode 1 : `Database::connexionDB()`

- **Fichier** : `src/Core/Database.php`
- **Rôle** : Assurer une connexion unique à la base de données avec mécanisme de fallback automatique PostgreSQL vers SQLite.
- **Explication ligne par ligne** : *(À compléter lors de l'implémentation)*

### Méthode 2 : `VenteService::processSale()`

- **Fichier** : `src/Service/VenteService.php`
- **Rôle** : Gérer la transaction complète d'une vente avec contrôle du stock et de la limite de crédit.
- **Explication ligne par ligne** : *(À compléter lors de l'implémentation)*

### Méthode 3 : `DebtService::repayDebt()`

- **Fichier** : `src/Service/DebtService.php`
- **Rôle** : Gérer le remboursement partiel ou total d'une dette avec mise à jour des statuts.
- **Explication ligne par ligne** : *(À compléter lors de l'implémentation)*
