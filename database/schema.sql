PRAGMA foreign_keys = ON;

CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE utilisateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    adresse TEXT,
    telephone TEXT,
    role_id INTEGER NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    telephone TEXT NOT NULL UNIQUE,
    email TEXT UNIQUE,
    limite_credit REAL DEFAULT 0,
    solde_actuel REAL DEFAULT 0,
    CHECK (limite_credit >= 0),
    CHECK (solde_actuel >= 0)
);

CREATE TABLE fournisseurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    email TEXT UNIQUE,
    telephone TEXT NOT NULL,
    adresse TEXT
);

CREATE TABLE produits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    libelle TEXT NOT NULL,
    categorie TEXT,
    prix_vente REAL NOT NULL,
    cout_achat REAL NOT NULL,
    stock_initial INTEGER DEFAULT 0,
    stock_actuel INTEGER DEFAULT 0,
    seuil_alerte INTEGER DEFAULT 5,
    fournisseur_id INTEGER,
    CHECK (prix_vente >= 0),
    CHECK (cout_achat >= 0),
    CHECK (stock_actuel >= 0),
    CHECK (seuil_alerte >= 0),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE SET NULL
);

CREATE TABLE modes_paiement (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE ventes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_facture TEXT NOT NULL UNIQUE,
    client_id INTEGER NOT NULL,
    utilisateur_id INTEGER NOT NULL,
    montant_total REAL NOT NULL,
    montant_verse REAL DEFAULT 0,
    mode_reglement TEXT,
    statut TEXT DEFAULT 'EN_ATTENTE',
    date_vente DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_echeance DATETIME,
    CHECK (montant_total >= 0),
    CHECK (montant_verse >= 0),
    CHECK (statut IN ('EN_ATTENTE', 'PAYEE', 'ANNULEE', 'PARTIELLE')),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE RESTRICT
);

CREATE TABLE lignes_vente (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    vente_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantite INTEGER NOT NULL,
    prix_unitaire REAL NOT NULL,
    sous_total REAL NOT NULL,
    CHECK (quantite > 0),
    CHECK (prix_unitaire >= 0),
    CHECK (sous_total >= 0),
    FOREIGN KEY (vente_id) REFERENCES ventes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT
);

CREATE TABLE dettes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ref TEXT NOT NULL UNIQUE,
    vente_id INTEGER NOT NULL UNIQUE,
    client_id INTEGER NOT NULL,
    montant_initial REAL NOT NULL,
    montant_verse REAL DEFAULT 0,
    montant_restant REAL NOT NULL,
    date_echeance DATETIME,
    statut TEXT DEFAULT 'NON_SOLDEE',
    CHECK (montant_initial >= 0),
    CHECK (montant_verse >= 0),
    CHECK (montant_restant >= 0),
    CHECK (statut IN ('SOLDEE', 'NON_SOLDEE', 'EN_RETARD')),
    FOREIGN KEY (vente_id) REFERENCES ventes(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT
);

CREATE TABLE paiements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    dette_id INTEGER NOT NULL,
    utilisateur_id INTEGER,
    mode_paiement_id INTEGER NOT NULL,
    montant REAL NOT NULL,
    notes TEXT,
    reference TEXT,
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (montant > 0),
    FOREIGN KEY (dette_id) REFERENCES dettes(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    FOREIGN KEY (mode_paiement_id) REFERENCES modes_paiement(id) ON DELETE RESTRICT
);

CREATE TABLE approvisionnements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reference_bl TEXT NOT NULL UNIQUE,
    fournisseur_id INTEGER NOT NULL,
    utilisateur_id INTEGER,
    cout_total REAL NOT NULL,
    date_appro DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_reception DATETIME,
    statut TEXT DEFAULT 'EN_ATTENTE',
    CHECK (cout_total >= 0),
    CHECK (statut IN ('EN_ATTENTE', 'RECU', 'PARTIEL')),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE RESTRICT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

CREATE TABLE lignes_approvisionnement (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    approvisionnement_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantite_appro INTEGER NOT NULL,
    quantite_recue INTEGER DEFAULT 0,
    prix_achat REAL NOT NULL,
    sous_total REAL NOT NULL,
    CHECK (quantite_appro > 0),
    CHECK (quantite_recue >= 0),
    CHECK (prix_achat >= 0),
    CHECK (sous_total >= 0),
    FOREIGN KEY (approvisionnement_id) REFERENCES approvisionnements(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT
);

CREATE INDEX idx_utilisateur_role ON utilisateurs(role_id);
CREATE INDEX idx_vente_client ON ventes(client_id);
CREATE INDEX idx_vente_utilisateur ON ventes(utilisateur_id);
CREATE INDEX idx_ligne_vente_vente ON lignes_vente(vente_id);
CREATE INDEX idx_ligne_vente_produit ON lignes_vente(produit_id);
CREATE INDEX idx_dette_vente ON dettes(vente_id);
CREATE INDEX idx_dette_client ON dettes(client_id);
CREATE INDEX idx_paiement_dette ON paiements(dette_id);
CREATE INDEX idx_paiement_mode ON paiements(mode_paiement_id);
CREATE INDEX idx_appro_fournisseur ON approvisionnements(fournisseur_id);
CREATE INDEX idx_ligne_appro_appro ON lignes_approvisionnement(approvisionnement_id);
CREATE INDEX idx_ligne_appro_produit ON lignes_approvisionnement(produit_id);
CREATE INDEX idx_produit_fournisseur ON produits(fournisseur_id);
CREATE INDEX idx_ventes_statut ON ventes(statut);
CREATE INDEX idx_dettes_statut ON dettes(statut);
CREATE INDEX idx_dettes_ref ON dettes(ref);
CREATE INDEX idx_appro_reference_bl ON approvisionnements(reference_bl);

INSERT INTO roles (nom, description) VALUES
    ('admin', 'Administrateur - Accès complet'),
    ('vente', 'Chargé de Vente - Gestion des ventes et dettes'),
    ('stock', 'Chargé de Stock - Gestion des approvisionnements'),
    ('inventaire', 'Inventaire - Consultation des produits et stocks');

INSERT INTO modes_paiement (nom, description) VALUES
    ('Wave', 'Paiement mobile via Wave'),
    ('Orange Money', 'Paiement mobile via Orange Money'),
    ('Especes', 'Paiement en espèces'),
    ('Virement', 'Virement bancaire');

INSERT INTO utilisateurs (nom, prenom, email, password, telephone, adresse, role_id)
SELECT 'Sy', 'Rougui', 'admin@storemanager.sn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '775554433', 'Dakar, Sénégal', r.id
FROM roles r WHERE r.nom = 'admin';

CREATE VIEW v_dettes_clients AS
SELECT 
    c.id AS client_id,
    c.nom || ' ' || c.prenom AS client_nom,
    c.telephone,
    COUNT(d.id) AS nombre_dettes,
    SUM(d.montant_restant) AS total_du
FROM clients c
LEFT JOIN dettes d ON c.id = d.client_id AND d.statut != 'SOLDEE'
GROUP BY c.id, c.nom, c.prenom, c.telephone
HAVING SUM(d.montant_restant) > 0;

CREATE VIEW v_stock_produits AS
SELECT 
    p.id,
    p.code,
    p.libelle,
    p.prix_vente,
    p.stock_actuel,
    p.seuil_alerte,
    p.stock_actuel * p.prix_vente AS valeur_stock,
    CASE 
        WHEN p.stock_actuel <= p.seuil_alerte THEN 'ALERTE'
        ELSE 'OK'
    END AS statut_stock
FROM produits p;