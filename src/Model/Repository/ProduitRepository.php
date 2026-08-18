<?php

require_once dirname(__DIR__) . "/Entity/Produit.php";

class ProduitRepository
{
    private static function getPdo(): PDO
    {
        return Database::connexionDB();
    }

    public static function insert(Produit $produit): int
    {
        $pdo = self::getPdo();
        $sql = "INSERT INTO produits (code, libelle, categorie, prix_vente, cout_achat, stock_initial, stock_actuel, seuil_alerte, fournisseur_id)
                VALUES(:code, :libelle, :categorie, :prix_vente, :cout_achat, :stock_initial, :stock_actuel, :seuil_alerte, :fournisseur_id)";

        Database::executeUpdate($pdo, $sql, [
            'code' => $produit->getCode(),
            'libelle' => $produit->getLibelle(),
            'categorie' => $produit->getCategorie(),
            'prix_vente' => $produit->getPrixVente(),
            'cout_achat' => $produit->getCoutAchat(),
            'stock_initial' => $produit->getStockInitial(),
            'stock_actuel' => $produit->getStockActuel(),
            'seuil_alerte' => $produit->getSeuilAlerte(),
            'fournisseur_id' => $produit->getFournisseurId()
        ]);

        $id = (int) $pdo->lastInsertId();
        $produit->setId($id);
        return $id;
    }

    public static function selectById(int $id): ?Produit
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM produits WHERE id = :id";

        $produit = Database::executeQuery($pdo, $sql, ['id' => $id]);

        if (!$produit) return null;
        
        return self::toObjet($produit);
    }

    public static function selectByCode(string $code): ?Produit
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM produits WHERE code = :code";

        $produit = Database::executeQuery($pdo, $sql, ['code' => $code]);

        if (!$produit) return null;
        
        return self::toObjet($produit);
    }

    public static function selectLowStock(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM produits WHERE stock_actuel <= seuil_alerte ORDER BY stock_actuel ASC";

        $tableauProduits = Database::query($pdo, $sql, false);

        $produits = [];

        if (empty($tableauProduits)) return $produits;
        
        foreach ($tableauProduits as $produit) {
            $produits[] = self::toObjet($produit);
        }

        return $produits;
    }

    public static function selectByCategorie(string $categorie): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM produits WHERE categorie = :categorie ORDER BY libelle";

        $tableauProduits = Database::executeQuery($pdo, $sql, ['categorie' => $categorie], false);

        $produits = [];

        if (empty($tableauProduits)) return $produits;
        
        foreach ($tableauProduits as $produit) {
            $produits[] = self::toObjet($produit);
        }

        return $produits;
    }

    public static function selectByFournisseur(int $fournisseurId): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM produits WHERE fournisseur_id = :fournisseur_id ORDER BY libelle";

        $tableauProduits = Database::executeQuery($pdo, $sql, ['fournisseur_id' => $fournisseurId], false);

        $produits = [];

        if (empty($tableauProduits)) return $produits;
        
        foreach ($tableauProduits as $produit) {
            $produits[] = self::toObjet($produit);
        }

        return $produits;
    }

    public static function selectAll(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM produits ORDER BY libelle ASC";

        $tableauProduits = Database::query($pdo, $sql, false);

        $produits = [];

        if (empty($tableauProduits)) return $produits;
        
        foreach ($tableauProduits as $produit) {
            $produits[] = self::toObjet($produit);
        }

        return $produits;
    }

    public static function update(Produit $produit): bool
    {
        $pdo = self::getPdo();
        $sql = "UPDATE produits
                SET code = :code, libelle = :libelle, categorie = :categorie, 
                    prix_vente = :prix_vente, cout_achat = :cout_achat,
                    stock_initial = :stock_initial, stock_actuel = :stock_actuel,
                    seuil_alerte = :seuil_alerte, fournisseur_id = :fournisseur_id
                WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql,
            [
                'id' => $produit->getId(),
                'code' => $produit->getCode(),
                'libelle' => $produit->getLibelle(),
                'categorie' => $produit->getCategorie(),
                'prix_vente' => $produit->getPrixVente(),
                'cout_achat' => $produit->getCoutAchat(),
                'stock_initial' => $produit->getStockInitial(),
                'stock_actuel' => $produit->getStockActuel(),
                'seuil_alerte' => $produit->getSeuilAlerte(),
                'fournisseur_id' => $produit->getFournisseurId()
            ]
        );

        return $nbrRowsAffecte > 0 ? true : false;
    }

    public static function updateStock(int $id, int $quantite): bool
    {
        $pdo = self::getPdo();
        $sql = "UPDATE produits SET stock_actuel = stock_actuel + :quantite WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql, [
            'id' => $id,
            'quantite' => $quantite
        ]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    public static function delete(int $id): bool
    {
        $pdo = self::getPdo();
        $sql = "DELETE FROM produits WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql, ['id' => $id]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    private static function toObjet(array $produit): Produit
    {
        return new Produit(
            $produit['code'],
            $produit['libelle'],
            $produit['categorie'] ?? null,
            (float) $produit['prix_vente'],
            (float) $produit['cout_achat'],
            (int) $produit['fournisseur_id'],
            (int) $produit['stock_initial'],
            (int) $produit['stock_actuel'],
            (int) $produit['seuil_alerte'],
            (int) $produit['id']
        );
    }
}
