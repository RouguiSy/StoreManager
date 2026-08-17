<?php

require_once dirname(__DIR__) . "/Entity/Dette.php";
require_once dirname(__DIR__) . "/Entity/Paiement.php";

class DetteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connexionDB();
    }

    public function insert(Dette $dette): int
    {
        $sql = "INSERT INTO dettes (ref, vente_id, client_id, montant_initial, montant_verse, montant_restant, statut, date_echeance)
                VALUES (:ref, :vente_id, :client_id, :montant_initial, :montant_verse, :montant_restant, :statut, :date_echeance)";

        Database::executeUpdate($this->pdo, $sql, [
            'ref' => $dette->getRef(),
            'vente_id' => $dette->getVenteId(),
            'client_id' => $dette->getClientId(),
            'montant_initial' => $dette->getMontantInitial(),
            'montant_verse' => $dette->getMontantVerse(),
            'montant_restant' => $dette->getMontantRestant(),
            'statut' => $dette->getStatut(),
            'date_echeance' => $dette->getDateEcheance()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $dette->setId($id);
        return $id;
    }

    public function selectById(int $id): ?Dette
    {
        $sql = "SELECT * FROM dettes WHERE id = :id";
        $result = Database::executeQuery($this->pdo, $sql, ['id' => $id]);

        if (!$result) {
            return null;
        }

        return $this->toObjet($result);
    }

    public function selectByClient(int $clientId): array
    {
        $sql = "SELECT * FROM dettes WHERE client_id = :client_id ORDER BY created_at DESC";
        $results = Database::executeQuery($this->pdo, $sql, ['client_id' => $clientId], false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = $this->toObjet($row);
        }

        return $dettes;
    }

    public function selectDettesActives(): array
    {
        $sql = "SELECT * FROM dettes WHERE statut = 'NON_SOLDEE' ORDER BY created_at DESC";
        $results = Database::query($this->pdo, $sql, false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = $this->toObjet($row);
        }

        return $dettes;
    }

    public function selectDettesByClientActives(int $clientId): array
    {
        $sql = "SELECT * FROM dettes WHERE client_id = :client_id AND statut = 'NON_SOLDEE' ORDER BY created_at DESC";
        $results = Database::executeQuery($this->pdo, $sql, ['client_id' => $clientId], false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = $this->toObjet($row);
        }

        return $dettes;
    }

    public function selectAll(): array
    {
        $sql = "SELECT * FROM dettes ORDER BY created_at DESC";
        $results = Database::query($this->pdo, $sql, false);

        $dettes = [];
        foreach ($results as $row) {
            $dettes[] = $this->toObjet($row);
        }

        return $dettes;
    }

    public function update(Dette $dette): bool
    {
        $sql = "UPDATE dettes SET 
                    montant_verse = :montant_verse,
                    montant_restant = :montant_restant,
                    statut = :statut
                WHERE id = :id";

        $result = Database::executeUpdate($this->pdo, $sql, [
            'id' => $dette->getId(),
            'montant_verse' => $dette->getMontantVerse(),
            'montant_restant' => $dette->getMontantRestant(),
            'statut' => $dette->getStatut()
        ]);

        return $result > 0;
    }

    public function insertPaiement(Paiement $paiement): int
    {
        $sql = "INSERT INTO paiements (dette_id, utilisateur_id, mode_paiement_id, montant, notes, reference)
                VALUES (:dette_id, :utilisateur_id, :mode_paiement_id, :montant, :notes, :reference)";

        Database::executeUpdate($this->pdo, $sql, [
            'dette_id' => $paiement->getDetteId(),
            'utilisateur_id' => $paiement->getUtilisateurId(),
            'mode_paiement_id' => $paiement->getModePaiementId(),
            'montant' => $paiement->getMontant(),
            'notes' => $paiement->getNotes(),
            'reference' => $paiement->getReference()
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function getPaiementsByDette(int $detteId): array
    {
        $sql = "SELECT * FROM paiements WHERE dette_id = :dette_id ORDER BY date_paiement DESC";
        $results = Database::executeQuery($this->pdo, $sql, ['dette_id' => $detteId], false);

        return $results;
    }

    public function getTotalDettesParClient(int $clientId): float
    {
        $sql = "SELECT COALESCE(SUM(montant_restant), 0) as total FROM dettes WHERE client_id = :client_id AND statut = 'NON_SOLDEE'";
        $result = Database::executeQuery($this->pdo, $sql, ['client_id' => $clientId]);

        return $result ? (float) $result['total'] : 0;
    }

    private function toObjet(array $row): Dette
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
