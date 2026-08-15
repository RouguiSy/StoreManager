
# 📓 Journal de Développement (DEVLOG)

**Nom & Prénom** : Rougui Sy
**Projet** : StoreManager Pro (ERP PHP/POO)

---

## 1. Suivi Chronologique des Phases

### 🌃 [Vendredi - Phase 1] : Conception & BDD Fallback

#### 📌 Step 1.1 (19h00 - 20h30) : Conception UML

- **Heure de réalisation** : 19h00 - 20h30
- **Ce qui a été fait** :

  - J'ai commencé par créer le dossier `docs/diagrams` pour ranger tous mes diagrammes.
  - J'ai listé les 4 acteurs : Admin, Chargé de Vente, Chargé de Stock et Inventaire.
  - Pour chaque acteur, j'ai fait son diagramme de cas d'utilisation.
  - J'ai aussi fait le diagramme de classes avec toutes les entités.
- **Difficultés / Obstacles** :

  - J'ai aussi hésité sur la relation entre Vente et Dette. Je me suis demandé : est-ce qu'une vente peut avoir plusieurs dettes ? J'ai compris qu'une vente donne naissance à une seule dette quand le client paie en plusieurs fois. J'ai donc mis "0..1" côté Vente et "1" côté Dette.

#### 📌 Step 1.2 (20h30 - 22h00) : Schéma SQL PostgreSQL / SQLite

- **Heure de réalisation** : 20h30 - 22h00
- **Ce qui a été fait** :

  - J'ai créé le dossier `database` pour mes scripts SQL.
  - J'ai écrit le script PostgreSQL avec toutes mes tables, contraintes et index.
  - J'ai fait une version SQLite en adaptant la syntaxe.
  - J'ai ajouté des vues pour faciliter les rapports.
- **Difficultés / Obstacles** :

  - La plus grosse difficulté a été les différences entre PostgreSQL et SQLite. Par exemple, PostgreSQL utilise `SERIAL` tandis que SQLite utilise `AUTOINCREMENT`. J'ai dû réécrire plusieurs parties du script.
  - J'ai aussi découvert que SQLite n'active pas les clés étrangères par défaut. J'ai dû ajouter `PRAGMA foreign_keys = ON;` au début du fichier. Sans ça, mes contraintes ne fonctionnaient pas.

#### 📌 Step 1.3 (22h00 - 23h00) : Singleton Database & Fallback Automatique

- **Heure de réalisation** : 22h00 - 23h00
- **Ce qui a été fait** :

  - J'ai créé la classe `Database.php` dans `src/Core/`.
  - J'ai utilisé le pattern Singleton pour avoir une seule connexion.
  - J'ai ajouté le fallback : PostgreSQL et si ça échoue, SQLite.
- **Difficultés / Obstacles** :

  - Le plus dur a été de comprendre le pattern Singleton. Pourquoi on ne peut pas juste faire `new Database()` à chaque fois ? J'ai compris que ça évite d'ouvrir plusieurs connexions à la base, ce qui ralentirait l'application.
  - La gestion des exceptions a été un vrai casse-tête. Je ne voulais pas afficher des messages d'erreur techniques à l'utilisateur. J'ai donc capturé les exceptions et j'ai renvoyé un message simple.
  - J'ai eu un problème avec `dirname(__DIR__, 2)`. Je ne comprenais pas pourquoi ça remontait de deux dossiers. En fait, `__DIR__` est dans `src/Core/`, donc `dirname(__DIR__, 2)` remonte jusqu'à la racine du projet. Ça m'a pris du temps à comprendre.

---

### ☀️ [Samedi - Phase 2] : POO, Repositories & Ventes POS

#### 📌 Step 2.1 (09h00 - 11h00) : Entités POO Pure

- **Heure de réalisation** : 09h00 - 11h00
- **Ce qui a été fait** :

  - J'ai créé le dossier `src/Model/Entity` et toutes mes classes.
  - J'ai mis des attributs privés avec des getters et setters.
  - J'ai ajouté des méthodes métier.
- **Difficultés / Obstacles** :

  - La relation entre les entités m'a vraiment embrouillé. Par exemple, dans `Utilisateur`, je dois avoir à la fois `private int $roleId` et `private ?Role $role`. Pourquoi les deux ? J'ai compris que `$roleId` sert pour la base de données (clé étrangère) et `$role` sert pour la POO (objet). Le prof a insisté sur ce point.
  - J'ai fait une erreur au début : j'ai mis toutes les propriétés en public pour gagner du temps. Mais avais dit que c'était une mauvaise pratique. J'ai dû tout reprendre pour mettre des getters/setters.
  - Je ne savais pas comment gérer les collections. Par exemple, un `Client` peut avoir plusieurs `Dette`. J'ai utilisé un tableau `private array $dettes = [];` avec des méthodes `addDette()` et `getDettes()`.

#### 📌 Step 2.2 (11h00 - 13h00) : Repositories & SQL Sécurisé

- **Heure de réalisation** : 11h00 - 13h00
- **Ce qui a été fait** :

  - J'ai créé les repositories pour Client, Fournisseur et Produit.
  - J'ai utilisé des requêtes préparées avec paramètres nommés.
  - J'ai fait une méthode `toObjet()` pour convertir les données SQL en objets PHP.
- **Difficultés / Obstacles** :

  - La méthode `toObjet()` a été compliquée. Je dois faire correspondre chaque colonne de la base de données avec chaque attribut de ma classe. Si l'ordre des paramètres ne correspond pas, ça casse tout. J'ai dû vérifier plusieurs fois.
  - J'ai eu un bug où `selectAll()` ne me retournait qu'un seul résultat au lieu de tous. J'ai oublié de mettre `false` comme deuxième paramètre de `Database::query()`. Par défaut, elle retourne un seul résultat. Ça m'a pris 20 minutes à trouver.
  - Vous avais insisté sur la sécurité des requêtes. Pas de concaténation directe. J'ai dû revoir toutes mes requêtes pour vérifier que j'utilisais bien `:nom` ou `?` et que je passais les paramètres dans un tableau. C'était un peu long mais nécessaire.

#### 📌 Step 2.3 (14h00 - 17h00) : Service Métier Vente POS & Transaction SQL

- **Heure de réalisation** : 14h00 - 17h00
- **Ce qui a été fait** :

  - *(Pas encore réalisé)*
- **Difficultés / Obstacles** :

  - *(Pas encore réalisé)*

#### 📌 Step 2.4 (17h00 - 20h00) : Controller POS & Vue Caisse

- **Heure de réalisation** : 17h00 - 20h00
- **Ce qui a été fait** :

  - *(Pas encore réalisé)*
- **Difficultés / Obstacles** :

  - *(Pas encore réalisé)*

---

## 2. Autopsie de 3 Méthodes Clés (Indispensable pour l'oral)

### Méthode 1 : `Database::connexionDB()`

- **Fichier** : `src/Core/Database.php`
- **Rôle** : Assurer une connexion unique à la base avec fallback PostgreSQL → SQLite.
- **Explication ligne par ligne** : *(À compléter après la Phase 3)*

### Méthode 2 : `VenteService::processSale()`

- **Fichier** : `src/Service/VenteService.php`
- **Rôle** : Gérer une vente avec contrôle du stock et de la limite de crédit.
- **Explication ligne par ligne** : *(À compléter après la Phase 3)*

### Méthode 3 : `DebtService::repayDebt()`

- **Fichier** : `src/Service/DebtService.php`
- **Rôle** : Gérer le remboursement d'une dette et mettre à jour les statuts.
- **Explication ligne par ligne** : *(À compléter après la Phase 3)*

-
