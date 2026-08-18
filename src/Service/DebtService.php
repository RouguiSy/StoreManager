<?php

require_once dirname(__DIR__) . "/Model/Repository/DetteRepository.php";
require_once dirname(__DIR__) . "/Model/Repository/ClientRepository.php";
require_once dirname(__DIR__) . "/Model/Entity/Paiement.php";

class DebtService
{
    public static function repayDebt(int $detteId, float $montant, int $modePaiementId, ?int $utilisateurId = null, ?string $notes = null): array
    {
        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Le montant doit etre superieur a 0'];
        }

        $dette = DetteRepository::selectById($detteId);
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

        $pdo = Database::connexionDB();

        try {
            Database::beginTransaction($pdo);

            $dette->applyPayment($montant);
            DetteRepository::update($dette);

            $paiement = new Paiement(
                $detteId,
                $utilisateurId,
                $modePaiementId,
                $montant,
                $notes,
                null
            );
            DetteRepository::insertPaiement($paiement);

            ClientRepository::updateSolde($dette->getClientId(), -$montant);

            Database::commit($pdo);

            return [
                'success' => true,
                'message' => 'Remboursement effectue avec succes',
                'dette_id' => $detteId,
                'montant_paye' => $montant,
                'reste_du' => $dette->getMontantRestant(),
                'statut' => $dette->getStatut()
            ];

        } catch (Exception $e) {
            Database::rollBack($pdo);
            return [
                'success' => false,
                'message' => 'Erreur lors du remboursement: ' . $e->getMessage()
            ];
        }
    }

    public static function getDettesActives(): array
    {
        return DetteRepository::selectDettesActives();
    }

    public static function getDettesByClient(int $clientId): array
    {
        return DetteRepository::selectByClient($clientId);
    }

    public static function getDettesActivesByClient(int $clientId): array
    {
        return DetteRepository::selectDettesByClientActives($clientId);
    }

    public static function getTotalDettesByClient(int $clientId): float
    {
        return DetteRepository::getTotalDettesParClient($clientId);
    }

    public static function getPaiementsByDette(int $detteId): array
    {
        return DetteRepository::getPaiementsByDette($detteId);
    }

    public static function getDetteWithDetails(int $detteId): ?array
    {
        $dette = DetteRepository::selectById($detteId);
        if (!$dette) {
            return null;
        }

        $paiements = DetteRepository::getPaiementsByDette($detteId);

        return [
            'dette' => $dette,
            'paiements' => $paiements
        ];
    }
}
