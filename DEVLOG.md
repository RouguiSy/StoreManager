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
- **Modifications apportées au diagramme de classes** :

  - Au début, j'avais mis une relation directe entre `Vente` et `Client` avec une cardinalité "1" côté Client. Mais en réfléchissant, une vente peut être faite sans client (vente anonyme). J'ai donc changé la cardinalité en "0..1" côté Vente. Finalement, le prof m'a dit que c'était mieux d'avoir un client pour chaque vente pour le suivi, donc j'ai remis "1".
  - J'ai d'abord modélisé la relation entre `Utilisateur` et `Role` avec une composition forte. Mais en discutant avec le prof, j'ai compris qu'un utilisateur a juste un rôle, mais le rôle existe indépendamment. J'ai donc changé en une simple association.
  - Pour la relation entre `Vente` et `Dette`, j'ai longtemps hésité. Au début, je pensais qu'une vente pouvait avoir plusieurs dettes. Mais en réalité, une vente donne naissance à une seule dette quand le client paie en plusieurs fois. J'ai donc mis "1" côté Vente et "0..1" côté Dette.
  - J'ai aussi ajouté l'entité `PaiementFournisseur` que j'avais oubliée au départ. Dans le commerce, on doit aussi gérer les paiements aux fournisseurs, pas seulement les paiements clients.
- **Difficultés / Obstacles** :

  - J'ai eu du mal à bien comprendre les cardinalités. Par exemple, "1" signifie qu'un élément est obligatoire, "0..1" signifie qu'il est optionnel. J'ai dû réfléchir à chaque relation métier.
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

  - J'ai créé la classe `VenteService.php` dans `src/Service/`.
  - J'ai implémenté la méthode principale `processSale()` qui gère toute une vente.
  - Cette méthode vérifie que le panier n'est pas vide, que le client existe, que le stock est suffisant et que la limite de crédit n'est pas dépassée.
  - J'ai utilisé une **transaction PDO** pour garantir que toutes les opérations se font ou aucune ne se fait.
  - J'ai créé des méthodes privées pour insérer la vente, les lignes de vente, la dette si nécessaire, et mettre à jour le statut.
  - J'ai ajouté des méthodes pour générer automatiquement les numéros de facture et les références de dette.
  - J'ai aussi ajouté des méthodes utilitaires : `calculerTotalPanier()` et `verifierStock()`.
- **Difficultés / Obstacles** :

  - La gestion des transactions a été compliquée. Il fallait s'assurer que si une requête échoue, toutes les autres sont annulées. Sans ça, on aurait des données incohérentes.
  - J'ai eu du mal à bien comprendre l'ordre des opérations. J'ai d'abord vérifié le stock, puis j'ai inséré la vente, puis les lignes, puis j'ai décrémenté le stock, et enfin j'ai créé la dette si nécessaire.
  - La génération des numéros de facture m'a demandé de faire une requête pour compter les factures existantes du jour. J'ai utilisé `LIKE` avec un préfixe pour trouver les factures du jour.
  - Le plus gros problème : j'ai d'abord fait la décrémentation du stock avant la transaction. Du coup, si la vente échouait, le stock restait décrémenté. J'ai compris qu'il fallait tout mettre dans la transaction pour que tout soit annulé en cas d'erreur.

#### 📌 Step 2.4 (17h00 - 20h00) : Controller POS & Vue Caisse

- **Heure de réalisation** : 17h00 - 20h00
- **Ce qui a été fait** :

  - J'ai créé le dossier `src/Controller/` et la classe `POSController.php`.
  - J'ai créé le gestionnaire de sessions `SessionManager.php` dans `src/Core/` pour centraliser la gestion des sessions.
  - Le contrôleur gère plusieurs actions :
    - `index()` : Affiche la page de caisse
    - `addToCart()` : Ajoute un produit au panier (session)
    - `removeFromCart()` : Retire un produit du panier
    - `clearCart()` : Vide le panier
    - `setClient()` : Sélectionne un client
    - `setPayment()` : Définit le mode de règlement et le montant versé
    - `validateSale()` : Valide la vente en appelant `VenteService`
  - J'ai utilisé les sessions pour stocker le panier, le client sélectionné, le mode de règlement et le montant versé.
  - J'ai créé la vue `views/pos/index.php` avec une interface tactile.
  - J'ai intégré les messages de succès/erreur via les sessions.
- **Difficultés / Obstacles** :

  - J'ai dû bien réfléchir à la structure du panier en session : un tableau associatif `[produit_id => quantite]`.
  - Le passage des données entre le contrôleur et la vue m'a demandé de bien organiser les variables.

---

### 🚀 [Dimanche - Phase 3] : Dettes, Approvisionnements, Roles & Cloture

#### Step 3.1 (09h00 - 11h30) : Gestion des Dettes & Remboursements

- **Heure de realisation** : 09h00 - 11h30
- **Ce qui a ete fait** :

  - J'ai cree le repository `DetteRepository.php` avec les methodes :
    - `insert()` : Ajoute une dette
    - `selectById()` : Recherche par ID
    - `selectByClient()` : Dettes d'un client
    - `selectDettesActives()` : Toutes les dettes non soldees
    - `selectDettesByClientActives()` : Dettes actives d'un client
    - `selectAll()` : Toutes les dettes
    - `update()` : Met a jour une dette
    - `insertPaiement()` : Ajoute un paiement
    - `getPaiementsByDette()` : Historique des paiements
    - `getTotalDettesParClient()` : Total des dettes d'un client
    - `toObjet()` : Conversion SQL -> Objet
  - J'ai cree le service `DebtService.php` avec les methodes :
    - `repayDebt()` : Remboursement d'une dette avec transaction
    - `getDettesActives()` : Recupere toutes les dettes actives
    - `getDettesByClient()` : Dettes d'un client
    - `getDettesActivesByClient()` : Dettes actives d'un client
    - `getTotalDettesByClient()` : Total des dettes d'un client
    - `getPaiementsByDette()` : Historique des paiements
    - `getDetteWithDetails()` : Dette avec ses paiements
  - J'ai cree la vue `views/dettes/index.php` avec :
    - Statistiques : total dettes, nombre dettes, clients debiteurs, total rembourse
    - Liste des dettes actives avec informations client
    - Formulaire de remboursement avec choix du mode de paiement
    - Historique des paiements pour chaque dette
    - Filtrage (toutes / actives)

- **Difficultes / Obstacles** :

  - La gestion des transactions pour le remboursement : il fallait mettre a jour la dette, ajouter le paiement ET mettre a jour le solde du client.
  - Le calcul du total des dettes par client avec `COALESCE` pour eviter les null.
  - L'affichage dynamique des formulaires de remboursement avec toggle en JavaScript.
  - La recuperation des paiements pour l'historique m'a demande de bien structurer les donnees.
