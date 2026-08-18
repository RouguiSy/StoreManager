<?php

require_once dirname(__DIR__) . "/Core/SessionManager.php";
require_once dirname(__DIR__) . "/Service/DebtService.php";
require_once dirname(__DIR__) . "/Model/Repository/ClientRepository.php";
require_once dirname(__DIR__) . "/Model/Repository/DetteRepository.php";

class DettesController
{
    public function __construct()
    {
        SessionManager::init();
    }

    public function index(): void
    {
        $dettes = DebtService::getDettesActives();
        $modesPaiement = DetteRepository::getModesPaiement();
        $message = SessionManager::getMessage();

        $totalDettes = 0;
        $nombreDettes = count($dettes);
        $totalRembourse = 0;
        $clientsDebiteurs = [];

        foreach ($dettes as $dette) {
            $totalDettes += $dette->getMontantRestant();
            $totalRembourse += $dette->getMontantVerse();
            if (!in_array($dette->getClientId(), $clientsDebiteurs)) {
                $clientsDebiteurs[] = $dette->getClientId();
            }
        }

        $nombreClientsDebiteurs = count($clientsDebiteurs);

        require_once dirname(__DIR__, 2) . '/views/dettes/index.php';
    }

    public function repay(): void
    {
        $detteId = (int)($_POST['dette_id'] ?? 0);
        $montant = (float)($_POST['montant'] ?? 0);
        $modePaiementId = (int)($_POST['mode_paiement_id'] ?? 0);
        $notes = $_POST['notes'] ?? null;
        $utilisateurId = 1;

        if ($detteId <= 0 || $montant <= 0 || $modePaiementId <= 0) {
            SessionManager::setMessage('Donnees invalides', 'error');
            header('Location: index.php?action=dettes');
            exit;
        }

        $result = DebtService::repayDebt($detteId, $montant, $modePaiementId, $utilisateurId, $notes);

        if ($result['success']) {
            SessionManager::setMessage($result['message'], 'success');
        } else {
            SessionManager::setMessage($result['message'], 'error');
        }

        header('Location: index.php?action=dettes');
        exit;
    }
}
