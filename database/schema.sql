-- Active: 1785347854676@@127.0.0.1@5432@storemanager
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE utilisateurs (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    role_id INTEGER NOT NULL,
    CONSTRAINT fk_utilisateur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(255) UNIQUE,
    limite_credit DECIMAL(15,2) DEFAULT 0,
    solde_actuel DECIMAL(15,2) DEFAULT 0,
    CONSTRAINT chk_limite_credit CHECK (limite_credit >= 0),
    CONSTRAINT chk_solde_actuel CHECK (solde_actuel >= 0)
);

CREATE TABLE fournisseurs (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE,
    telephone VARCHAR(20) NOT NULL,
    adresse TEXT
);

CREATE TABLE produits (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    libelle VARCHAR(255) NOT NULL,
    categorie VARCHAR(100),
    prix_vente DECIMAL(15,2) NOT NULL,
    cout_achat DECIMAL(15,2) NOT NULL,
    stock_initial INTEGER DEFAULT 0,
    stock_actuel INTEGER DEFAULT 0,
    seuil_alerte INTEGER DEFAULT 5,
    fournisseur_id INTEGER,
    CONSTRAINT chk_prix_vente CHECK (prix_vente >= 0),
    CONSTRAINT chk_cout_achat CHECK (cout_achat >= 0),
    CONSTRAINT chk_stock_actuel CHECK (stock_actuel >= 0),
    CONSTRAINT chk_seuil_alerte CHECK (seuil_alerte >= 0),
    CONSTRAINT fk_produit_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE SET NULL
);

CREATE TABLE modes_paiement (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE ventes (
    id SERIAL PRIMARY KEY,
    numero_facture VARCHAR(50) NOT NULL UNIQUE,
    client_id INTEGER NOT NULL,
    utilisateur_id INTEGER NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    montant_verse DECIMAL(15,2) DEFAULT 0,
    mode_reglement VARCHAR(50),
    statut VARCHAR(20) DEFAULT 'EN_ATTENTE',
    date_vente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_echeance TIMESTAMP,
    CONSTRAINT chk_montant_total CHECK (montant_total >= 0),
    CONSTRAINT chk_montant_verse CHECK (montant_verse >= 0),
    CONSTRAINT chk_statut_vente CHECK (statut IN ('EN_ATTENTE', 'PAYEE', 'ANNULEE', 'PARTIELLE')),
    CONSTRAINT fk_vente_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    CONSTRAINT fk_vente_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE RESTRICT
);

CREATE TABLE lignes_vente (
    id SERIAL PRIMARY KEY,
    vente_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantite INTEGER NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    sous_total DECIMAL(15,2) NOT NULL,
    CONSTRAINT chk_quantite CHECK (quantite > 0),
    CONSTRAINT chk_prix_unitaire CHECK (prix_unitaire >= 0),
    CONSTRAINT chk_sous_total CHECK (sous_total >= 0),
    CONSTRAINT fk_ligne_vente_vente FOREIGN KEY (vente_id) REFERENCES ventes(id) ON DELETE CASCADE,
    CONSTRAINT fk_ligne_vente_produit FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT
);

CREATE TABLE dettes (
    id SERIAL PRIMARY KEY,
    ref VARCHAR(50) NOT NULL UNIQUE,
    vente_id INTEGER NOT NULL UNIQUE,
    client_id INTEGER NOT NULL,
    montant_initial DECIMAL(15,2) NOT NULL,
    montant_verse DECIMAL(15,2) DEFAULT 0,
    montant_restant DECIMAL(15,2) NOT NULL,
    date_echeance TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'NON_SOLDEE',
    CONSTRAINT chk_montant_initial CHECK (montant_initial >= 0),
    CONSTRAINT chk_montant_verse CHECK (montant_verse >= 0),
    CONSTRAINT chk_montant_restant CHECK (montant_restant >= 0),
    CONSTRAINT chk_statut_dette CHECK (statut IN ('SOLDEE', 'NON_SOLDEE', 'EN_RETARD')),
    CONSTRAINT fk_dette_vente FOREIGN KEY (vente_id) REFERENCES ventes(id) ON DELETE RESTRICT,
    CONSTRAINT fk_dette_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT
);

CREATE TABLE paiements (
    id SERIAL PRIMARY KEY,
    dette_id INTEGER NOT NULL,
    utilisateur_id INTEGER,
    mode_paiement_id INTEGER NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    notes TEXT,
    reference VARCHAR(50),
    date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_montant_paiement CHECK (montant > 0),
    CONSTRAINT fk_paiement_dette FOREIGN KEY (dette_id) REFERENCES dettes(id) ON DELETE CASCADE,
    CONSTRAINT fk_paiement_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    CONSTRAINT fk_paiement_mode FOREIGN KEY (mode_paiement_id) REFERENCES modes_paiement(id) ON DELETE RESTRICT
);

CREATE TABLE approvisionnements (
    id SERIAL PRIMARY KEY,
    reference_bl VARCHAR(50) NOT NULL UNIQUE,
    fournisseur_id INTEGER NOT NULL,
    utilisateur_id INTEGER,
    cout_total DECIMAL(15,2) NOT NULL,
    date_appro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_reception TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'EN_ATTENTE',
    CONSTRAINT chk_cout_total CHECK (cout_total >= 0),
    CONSTRAINT chk_statut_appro CHECK (statut IN ('EN_ATTENTE', 'RECU', 'PARTIEL')),
    CONSTRAINT fk_appro_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE RESTRICT,
    CONSTRAINT fk_appro_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

CREATE TABLE lignes_approvisionnement (
    id SERIAL PRIMARY KEY,
    approvisionnement_id INTEGER NOT NULL,
    produit_id INTEGER NOT NULL,
    quantite_appro INTEGER NOT NULL,
    quantite_recue INTEGER DEFAULT 0,
    prix_achat DECIMAL(15,2) NOT NULL,
    sous_total DECIMAL(15,2) NOT NULL,
    CONSTRAINT chk_quantite_appro CHECK (quantite_appro > 0),
    CONSTRAINT chk_quantite_recue CHECK (quantite_recue >= 0),
    CONSTRAINT chk_prix_achat CHECK (prix_achat >= 0),
    CONSTRAINT chk_sous_total_appro CHECK (sous_total >= 0),
    CONSTRAINT fk_ligne_appro_appro FOREIGN KEY (approvisionnement_id) REFERENCES approvisionnements(id) ON DELETE CASCADE,
    CONSTRAINT fk_ligne_appro_produit FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT
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