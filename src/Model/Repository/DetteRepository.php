<?php

require_once dirname(__DIR__) . "/Entity/Dette.php";
require_once dirname(__DIR__) . "/Entity/Paiement.php";
require_once dirname(__DIR__, 2) . "/Core/Database.php";

class DetteRepository
{
    private static function getPdo(): PDO
    {
        return Database::connexionDB();
    }

    public static function insert(Dette $dette): int
    {
        $pdo = self::getPdo();
        $sql = "INSERT INTO dettes (ref, vente_id, client_id, montant_initial, montant_verse, montant_restant, statut, date_echeance)
                VALUES (:ref, :vente_id, :client_id, :montant_initial, :montant_verse, :montant_restant, :statut, :date_echeance)";

        Database::executeUpdate($pdo, $sql, [
            'ref' => $dette->getRef(),
            'vente_id' => $dette->getVenteId(),
            'client_id' => $dette->getClientId(),
            'montant_initial' => $dette->getMontantInitial(),
            'montant_verse' => $dette->getMontantVerse(),
            'montant_restant' => $dette->getMontantRestant(),
            'statut' => $dette->getStatut(),
            'date_echeance' => $dette->getDateEcheance()
        ]);

        $id = (int) $pdo->lastInsertId();
        $dette->setId($id);
        return $id;
    }

    public static function selectById(int $id): ?Dette
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM dettes WHERE id = :id";
        $result = Database::executeQuery($pdo, $sql, ['id' => $id]);

        if (!$result) {
            return null;
        }

        return self::toObjet($result);
    }

    public static function selectByClient(int $clientId): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM dettes WHERE client_id = :client_id ORDER BY created_at DESC";
        $results = Database::executeQuery($pdo, $sql, ['client_id' => $clientId], false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = self::toObjet($row);
        }

        return $dettes;
    }

    public static function selectDettesActives(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM dettes WHERE statut = 'NON_SOLDEE' ORDER BY created_at DESC";
        $results = Database::query($pdo, $sql, false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = self::toObjet($row);
        }

        return $dettes;
    }

    public static function selectDettesByClientActives(int $clientId): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM dettes WHERE client_id = :client_id AND statut = 'NON_SOLDEE' ORDER BY created_at DESC";
        $results = Database::executeQuery($pdo, $sql, ['client_id' => $clientId], false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = self::toObjet($row);
        }

        return $dettes;
    }

    public static function selectAll(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM dettes ORDER BY created_at DESC";
        $results = Database::query($pdo, $sql, false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = self::toObjet($row);
        }

        return $dettes;
    }

    public static function update(Dette $dette): bool
    {
        $pdo = self::getPdo();
        $sql = "UPDATE dettes SET 
                    montant_verse = :montant_verse,
                    montant_restant = :montant_restant,
                    statut = :statut
                WHERE id = :id";

        $result = Database::executeUpdate($pdo, $sql, [
            'id' => $dette->getId(),
            'montant_verse' => $dette->getMontantVerse(),
            'montant_restant' => $dette->getMontantRestant(),
            'statut' => $dette->getStatut()
        ]);

        return $result > 0;
    }

    public static function insertPaiement(Paiement $paiement): int
    {
        $pdo = self::getPdo();
        $sql = "INSERT INTO paiements (dette_id, utilisateur_id, mode_paiement_id, montant, notes, reference)
                VALUES (:dette_id, :utilisateur_id, :mode_paiement_id, :montant, :notes, :reference)";

        Database::executeUpdate($pdo, $sql, [
            'dette_id' => $paiement->getDetteId(),
            'utilisateur_id' => $paiement->getUtilisateurId(),
            'mode_paiement_id' => $paiement->getModePaiementId(),
            'montant' => $paiement->getMontant(),
            'notes' => $paiement->getNotes(),
            'reference' => $paiement->getReference()
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function getPaiementsByDette(int $detteId): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM paiements WHERE dette_id = :dette_id ORDER BY date_paiement DESC";
        $results = Database::executeQuery($pdo, $sql, ['dette_id' => $detteId], false);

        return $results;
    }

    public static function getTotalDettesParClient(int $clientId): float
    {
        $pdo = self::getPdo();
        $sql = "SELECT COALESCE(SUM(montant_restant), 0) as total FROM dettes WHERE client_id = :client_id AND statut = 'NON_SOLDEE'";
        $result = Database::executeQuery($pdo, $sql, ['client_id' => $clientId]);

        return $result ? (float) $result['total'] : 0;
    }

    public static function getModesPaiement(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM modes_paiement ORDER BY nom";
        $results = Database::query($pdo, $sql, false);
        return $results;
    }

    private static function toObjet(array $row): Dette
    {
        return new Dette(
            $row['ref'],
            (int) $row['vente_id'],
            (int) $row['client_id'],
            (float) $row['montant_initial'],
            (float) $row['montant_verse'],
            $row['statut'],
            $row['date_echeance'],
            (int) $row['id']
        );
    }
}
