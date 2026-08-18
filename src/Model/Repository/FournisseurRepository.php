<?php

require_once dirname(__DIR__) . "/Entity/Fournisseur.php";

class FournisseurRepository
{
    private static function getPdo(): PDO
    {
        return Database::connexionDB();
    }

    public static function insert(Fournisseur $fournisseur): int
    {
        $pdo = self::getPdo();
        $sql = "INSERT INTO fournisseurs (nom, email, telephone, adresse)
                VALUES(:nom, :email, :telephone, :adresse)";

        Database::executeUpdate($pdo, $sql, [
            'nom' => $fournisseur->getNom(),
            'email' => $fournisseur->getEmail(),
            'telephone' => $fournisseur->getTelephone(),
            'adresse' => $fournisseur->getAdresse()
        ]);

        $id = (int) $pdo->lastInsertId();
        $fournisseur->setId($id);
        return $id;
    }

    public static function selectById(int $id): ?Fournisseur
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM fournisseurs WHERE id = :id";

        $fournisseur = Database::executeQuery($pdo, $sql, ['id' => $id]);

        if (!$fournisseur) return null;
        
        return self::toObjet($fournisseur);
    }

    public static function selectByNom(string $nom): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM fournisseurs WHERE nom ILIKE :nom ORDER BY nom";

        $tableauFournisseurs = Database::executeQuery($pdo, $sql, ['nom' => '%' . $nom . '%'], false);

        $fournisseurs = [];

        if (empty($tableauFournisseurs)) return $fournisseurs;
        
        foreach ($tableauFournisseurs as $fournisseur) {
            $fournisseurs[] = self::toObjet($fournisseur);
        }

        return $fournisseurs;
    }

    public static function selectAll(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM fournisseurs ORDER BY nom ASC";

        $tableauFournisseurs = Database::query($pdo, $sql, false);

        $fournisseurs = [];

        if (empty($tableauFournisseurs)) return $fournisseurs;
        
        foreach ($tableauFournisseurs as $fournisseur) {
            $fournisseurs[] = self::toObjet($fournisseur);
        }

        return $fournisseurs;
    }

    public static function update(Fournisseur $fournisseur): bool
    {
        $pdo = self::getPdo();
        $sql = "UPDATE fournisseurs
                SET nom = :nom, email = :email, telephone = :telephone, adresse = :adresse
                WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql,
            [
                'id' => $fournisseur->getId(),
                'nom' => $fournisseur->getNom(),
                'email' => $fournisseur->getEmail(),
                'telephone' => $fournisseur->getTelephone(),
                'adresse' => $fournisseur->getAdresse()
            ]
        );

        return $nbrRowsAffecte > 0 ? true : false;
    }

    public static function delete(int $id): bool
    {
        $pdo = self::getPdo();
        $sql = "DELETE FROM fournisseurs WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql, ['id' => $id]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    private static function toObjet(array $fournisseur): Fournisseur
    {
        return new Fournisseur(
            $fournisseur['nom'],
            $fournisseur['email'] ?? null,
            $fournisseur['telephone'],
            $fournisseur['adresse'] ?? null,
            (int) $fournisseur['id']
        );
    }
}
