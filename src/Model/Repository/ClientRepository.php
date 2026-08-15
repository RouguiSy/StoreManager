<?php

require_once dirname(__DIR__) . "/Entity/Client.php";

class ClientRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connexionDB();
    }

    public function insert(Client $client): int
    {
        $sql = "INSERT INTO clients (nom, prenom, telephone, email, limite_credit, solde_actuel)
                VALUES(:nom, :prenom, :telephone, :email, :limite_credit, :solde_actuel)";

        Database::executeUpdate($this->pdo, $sql, [
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'telephone' => $client->getTelephone(),
            'email' => $client->getEmail(),
            'limite_credit' => $client->getLimiteCredit(),
            'solde_actuel' => $client->getSoldeActuel()
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $client->setId($id);
        return $id;
    }

    public function selectById(int $id): ?Client
    {
        $sql = "SELECT * FROM clients WHERE id = :id";

        $client = Database::executeQuery($this->pdo, $sql, ['id' => $id]);

        if (!$client) return null;
        
        return $this->toObjet($client);
    }

    public function selectByTelephone(string $telephone): ?Client
    {
        $sql = "SELECT * FROM clients WHERE telephone = :telephone";

        $client = Database::executeQuery($this->pdo, $sql, ['telephone' => $telephone]);

        if (!$client) return null;
        
        return $this->toObjet($client);
    }

    public function selectAll(): array
    {
        $sql = "SELECT * FROM clients ORDER BY nom ASC";

        $tableauClients = Database::query($this->pdo, $sql, false);

        $clients = [];

        if (empty($tableauClients)) return $clients;
        
        foreach ($tableauClients as $client) {
            $clients[] = $this->toObjet($client);
        }

        return $clients;
    }

    public function selectDebiteurs(): array
    {
        $sql = "SELECT c.* FROM clients c 
                INNER JOIN dettes d ON c.id = d.client_id 
                WHERE d.statut = 'NON_SOLDEE' 
                GROUP BY c.id 
                HAVING SUM(d.montant_restant) > 0";

        $tableauClients = Database::query($this->pdo, $sql, false);

        $clients = [];

        if (empty($tableauClients)) return $clients;
        
        foreach ($tableauClients as $client) {
            $clients[] = $this->toObjet($client);
        }

        return $clients;
    }

    public function getSoldeDisponible(int $id): float
    {
        $sql = "SELECT (limite_credit - solde_actuel) AS solde_disponible FROM clients WHERE id = :id";
        $result = Database::executeQuery($this->pdo, $sql, ['id' => $id]);

        return $result ? (float) $result['solde_disponible'] : 0;
    }

    public function update(Client $client): bool
    {
        $sql = "UPDATE clients
                SET nom = :nom, prenom = :prenom, telephone = :telephone, email = :email, 
                    limite_credit = :limite_credit, solde_actuel = :solde_actuel
                WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($this->pdo, $sql,
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

    public function updateSolde(int $id, float $montant): bool
    {
        $sql = "UPDATE clients SET solde_actuel = solde_actuel + :montant WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($this->pdo, $sql, [
            'id' => $id,
            'montant' => $montant
        ]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM clients WHERE id = :id";

        $nbrRowsAffecte = Database::executeUpdate($this->pdo, $sql, ['id' => $id]);

        return $nbrRowsAffecte > 0 ? true : false;
    }

    private function toObjet(array $client): Client
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
