<?php

require_once dirname(__DIR__) . "/Model/Repository/DetteRepository.php";
require_once dirname(__DIR__) . "/Model/Repository/ClientRepository.php";
require_once dirname(__DIR__) . "/Model/Entity/Paiement.php";

class DebtService
{
    private DetteRepository $detteRepo;
    private ClientRepository $clientRepo;
    private PDO $pdo;

    public function __construct()
    {
        $this->detteRepo = new DetteRepository();
        $this->clientRepo = new ClientRepository();
        $this->pdo = Database::connexionDB();
    }

    public function repayDebt(int $detteId, float $montant, int $modePaiementId, ?int $utilisateurId = null, ?string $notes = null): array
    {
        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Le montant doit etre superieur a 0'];
        }

        $dette = $this->detteRepo->selectById($detteId);
        if (!$dette) {
            return ['success' => false, 'message' => 'Dette introuvable'];
        }

        if ($dette->isSold()) {
            return ['success' => false, 'message' => 'Cette dette est deja soldee'];
        }

        $resteDu = $dette->getMontantRestant();
        if ($montant > $resteDu) {
            return [
                'success' => false,
                'message' => 'Le montant depasse le reste dû. Reste dû: ' . number_format($resteDu, 0, ',', ' ') . ' FCFA'
            ];
        }

        try {
            Database::beginTransaction($this->pdo);

            $dette->applyPayment($montant);
            $this->detteRepo->update($dette);

            $paiement = new Paiement(
                $detteId,
                $utilisateurId,
                $modePaiementId,
                $montant,
                $notes,
                null
            );
            $this->detteRepo->insertPaiement($paiement);

            $this->clientRepo->updateSolde($dette->getClientId(), -$montant);

            Database::commit($this->pdo);

            return [
                'success' => true,
                'message' => 'Remboursement effectue avec succes',
                'dette_id' => $detteId,
                'montant_paye' => $montant,
                'reste_du' => $dette->getMontantRestant(),
                'statut' => $dette->getStatut()
            ];

        } catch (Exception $e) {
            Database::rollBack($this->pdo);
            return [
                'success' => false,
                'message' => 'Erreur lors du remboursement: ' . $e->getMessage()
            ];
        }
    }

    public function getDettesActives(): array
    {
        return $this->detteRepo->selectDettesActives();
    }

    public function getDettesByClient(int $clientId): array
    {
        return $this->detteRepo->selectByClient($clientId);
    }

    public function getDettesActivesByClient(int $clientId): array
    {
        return $this->detteRepo->selectDettesByClientActives($clientId);
    }

    public function getTotalDettesByClient(int $clientId): float
    {
        return $this->detteRepo->getTotalDettesParClient($clientId);
    }

    public function getPaiementsByDette(int $detteId): array
    {
        return $this->detteRepo->getPaiementsByDette($detteId);
    }

    public function getDetteWithDetails(int $detteId): ?array
    {
        $dette = $this->detteRepo->selectById($detteId);
        if (!$dette) {
            return null;
        }

        $paiements = $this->detteRepo->getPaiementsByDette($detteId);

        return [
            'dette' => $dette,
            'paiements' => $paiements
        ];
    }
}
