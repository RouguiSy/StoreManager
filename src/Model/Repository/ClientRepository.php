<?php

require_once dirname(__DIR__) . "/Entity/Client.php";

class ClientRepository
{
    private static function getPdo(): PDO
    {
        return Database::connexionDB();
    }

    public static function insert(Client $client): int
    {
        $pdo = self::getPdo();
        $sql = "INSERT INTO clients (nom, prenom, telephone, email, limite_credit, solde_actuel)
                VALUES(:nom, :prenom, :telephone, :email, :limite_credit, :solde_actuel)";

        Database::executeUpdate($pdo, $sql, [
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'telephone' => $client->getTelephone(),
            'email' => $client->getEmail(),
            'limite_credit' => $client->getLimiteCredit(),
            'solde_actuel' => $client->getSoldeActuel()
        ]);

        $id = (int) $pdo->lastInsertId();
        $client->setId($id);
        return $id;
    }

    public static function selectById(int $id): ?Client
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM clients WHERE id = :id";

        $client = Database::executeQuery($pdo, $sql, ['id' => $id]);

        if (!$client) return null;
        
        return self::toObjet($client);
    }

    public static function selectByTelephone(string $telephone): ?Client
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM clients WHERE telephone = :telephone";

        $client = Database::executeQuery($pdo, $sql, ['telephone' => $telephone]);

        if (!$client) return null;
        
        return self::toObjet($client);
    }

    public static function selectAll(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM clients ORDER BY nom ASC";

        $tableauClients = Database::query($pdo, $sql, false);

        $clients = [];

        if (empty($tableauClients)) return $clients;
        
        foreach ($tableauClients as $client) {
            $clients[] = self::toObjet($client);
        }

        return $clients;
    }

    public static function selectDebiteurs(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT c.* FROM clients c 
                INNER JOIN dettes d ON c.id = d.client_id 
                WHERE d.statut = 'NON_SOLDEE' 
                GROUP BY c.id 
                HAVING SUM(d.montant_restant) > 0";

        $tableauClients = Database::query($pdo, $sql, false);

        $clients = [];

        if (empty($tableauClients)) return $clients;
        
        foreach ($tableauClients as $client) {
            $clients[] = self::toObjet($client);
        }

        return $clients;
    }

    public static function getSoldeDisponible(int $id): float
    {
        $pdo = self::getPdo();
        $sql = "SELECT (limite_credit - solde_actuel) AS solde_disponible FROM clients WHERE id = :id";
        $result = Database::executeQuery($pdo, $sql, ['id' => $id]);

        return $result ? (float) $result['solde_disponible'] : 0;
    }

    public static function update(Client $client): bool
    {
        $pdo = self::getPdo();
        $sql = "UPDATE clients
                SET nom = :nom, prenom = :prenom, telephone = :telephone, email = :email, 
                    limite_credit = :limite_credit, solde_actuel = :solde_actuel
                WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql,
            [
                'id' => $client->getId(),
                'nom' => $client->getNom(),
                'prenom' => $client->getPrenom(),
                'telephone' => $client->getTelephone(),
                'email' => $client->getEmail(),
                'limite_credit' => $client->getLimiteCredit(),
                'solde_actuel' => $client->getSoldeActuel()
            ]
        );

        return $nbrRowsAffecte > 0 ? true : false;
    }

    public static function updateSolde(int $id, float $montant): bool
    {
        $pdo = self::getPdo();
        $sql = "UPDATE clients SET solde_actuel = solde_actuel + :montant WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql, [
            'id' => $id,
            'montant' => $montant
        ]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    public static function delete(int $id): bool
    {
        $pdo = self::getPdo();
        $sql = "DELETE FROM clients WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($pdo, $sql, ['id' => $id]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    private static function toObjet(array $client): Client
    {
        return new Client(
            $client['nom'],
            $client['prenom'],
            $client['telephone'],
            $client['email'] ?? null,
            (float) $client['limite_credit'],
            (float) $client['solde_actuel'],
            (int) $client['id']
        );
    }
}
