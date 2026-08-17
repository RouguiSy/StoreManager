<?php

require_once dirname(__DIR__) . "/Core/SessionManager.php";
require_once dirname(__DIR__) . "/Service/VenteService.php";
require_once dirname(__DIR__) . "/Model/Repository/ClientRepository.php";
require_once dirname(__DIR__) . "/Model/Repository/ProduitRepository.php";

class POSController
{
    private VenteService $venteService;
    private ClientRepository $clientRepo;
    private ProduitRepository $produitRepo;

    public function __construct()
    {
        SessionManager::init();
        $this->venteService = new VenteService();
        $this->clientRepo = new ClientRepository();
        $this->produitRepo = new ProduitRepository();
    }

    public function index(): void
    {
        $clients = $this->clientRepo->selectAll();
        $produits = $this->produitRepo->selectAll();
        $panier = SessionManager::getPanier();
        $clientId = SessionManager::getClientId();
        $modeReglement = SessionManager::getModeReglement();
        $montantVerse = SessionManager::getMontantVerse();
        $message = SessionManager::getMessage();
        
        $total = $this->venteService->calculerTotalPanier($panier);
        $utilisateurId = 1;
        
        require_once dirname(__DIR__, 2) . '/views/pos/index.php';
    }

    public function addToCart(): void
    {
        $produitId = (int)($_POST['produit_id'] ?? 0);
        $quantite = (int)($_POST['quantite'] ?? 0);
        
        if ($produitId <= 0 || $quantite <= 0) {
            SessionManager::setMessage('Données invalides', 'error');
            header('Location: index.php');
            exit;
        }
        
        $produit = $this->produitRepo->selectById($produitId);
        if (!$produit) {
            SessionManager::setMessage('Produit introuvable', 'error');
            header('Location: index.php');
            exit;
        }
        
        $panier = SessionManager::getPanier();
        $quantitePanier = $panier[$produitId] ?? 0;
        $stockActuel = $produit->getStockActuel();
        
        if ($stockActuel < $quantite + $quantitePanier) {
            SessionManager::setMessage(
                "Stock insuffisant pour {$produit->getLibelle()}. Disponible: $stockActuel",
                'error'
            );
            header('Location: index.php');
            exit;
        }
        
        if (isset($panier[$produitId])) {
            $panier[$produitId] += $quantite;
        } else {
            $panier[$produitId] = $quantite;
        }
        SessionManager::setPanier($panier);
        
        SessionManager::setMessage(
            "{$produit->getLibelle()} ajouté au panier !",
            'success'
        );
        header('Location: index.php');
        exit;
    }

    public function removeFromCart(): void
    {
        $produitId = (int)($_GET['id'] ?? 0);
        
        if ($produitId > 0) {
            SessionManager::removeFromPanier($produitId);
            SessionManager::setMessage('Article retiré du panier', 'success');
        }
        
        header('Location: index.php');
        exit;
    }

    public function clearCart(): void
    {
        SessionManager::clearPanier();
        SessionManager::setMessage('Panier vidé', 'success');
        header('Location: index.php');
        exit;
    }

    public function setClient(): void
    {
        $clientId = (int)($_POST['client_id'] ?? 0);
        
        if ($clientId > 0) {
            $client = $this->clientRepo->selectById($clientId);
            if ($client) {
                SessionManager::setClientId($clientId);
                SessionManager::setMessage(
                    "Client {$client->getNomComplet()} sélectionné",
                    'success'
                );
            } else {
                SessionManager::setMessage('Client introuvable', 'error');
            }
        } else {
            SessionManager::setClientId(null);
            SessionManager::setMessage('Aucun client sélectionné', 'warning');
        }
        
        header('Location: index.php');
        exit;
    }

    public function setPayment(): void
    {
        $modeReglement = $_POST['mode_reglement'] ?? 'Wave';
        $montantVerse = (float)($_POST['montant_verse'] ?? 0);
        
        SessionManager::setModeReglement($modeReglement);
        SessionManager::setMontantVerse($montantVerse);
        
        header('Location: index.php');
        exit;
    }

    public function validateSale(): void
    {
        $panier = SessionManager::getPanier();
        $clientId = SessionManager::getClientId();
        $modeReglement = SessionManager::getModeReglement();
        $montantVerse = SessionManager::getMontantVerse();
        $utilisateurId = 1;
        
        if (empty($panier)) {
            SessionManager::setMessage('Panier vide', 'error');
            header('Location: index.php');
            exit;
        }
        
        if (!$clientId) {
            SessionManager::setMessage('Veuillez sélectionner un client', 'error');
            header('Location: index.php');
            exit;
        }
        
        $result = $this->venteService->processSale(
            $clientId,
            $panier,
            $modeReglement,
            $montantVerse,
            $utilisateurId
        );
        
        if ($result['success']) {
            SessionManager::setMessage(
                "Vente enregistrée - Facture: {$result['numero_facture']} - Total: " . 
                number_format($result['montant_total'], 0, ',', ' ') . " F",
                'success'
            );
            SessionManager::clearPanier();
            SessionManager::setMontantVerse(0);
        } else {
            SessionManager::setMessage($result['message'], 'error');
        }
        
        header('Location: index.php');
        exit;
    }

    public function getProduits(): array
    {
        return $this->produitRepo->selectAll();
    }

    public function getClients(): array
    {
        return $this->clientRepo->selectAll();
    }

    public function getPanier(): array
    {
        return SessionManager::getPanier();
    }
}
