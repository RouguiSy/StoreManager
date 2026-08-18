<?php

require_once dirname(__DIR__) . "/Model/Repository/ClientRepository.php";
require_once dirname(__DIR__) . "/Model/Repository/ProduitRepository.php";
require_once dirname(__DIR__) . "/Model/Entity/Vente.php";
require_once dirname(__DIR__) . "/Model/Entity/LigneVente.php";
require_once dirname(__DIR__) . "/Model/Entity/Dette.php";

class VenteService
{
    public static function processSale(int $clientId, array $panier, string $modeReglement, float $montantVerse, int $utilisateurId): array
    {
        if (empty($panier)) {
            return ['success' => false, 'message' => 'Le panier est vide', 'vente_id' => null];
        }

        $clientRepo = new ClientRepository();
        $produitRepo = new ProduitRepository();
        $pdo = Database::connexionDB();

        $client = $clientRepo->selectById($clientId);
        if (!$client) {
            return ['success' => false, 'message' => 'Client introuvable', 'vente_id' => null];
        }

        $lignesVente = [];
        $montantTotal = 0;

        foreach ($panier as $produitId => $quantite) {
            $produit = $produitRepo->selectById((int)$produitId);
            if (!$produit) {
                return ['success' => false, 'message' => "Produit ID $produitId introuvable", 'vente_id' => null];
            }

            if ($produit->getStockActuel() < $quantite) {
                return [
                    'success' => false,
                    'message' => "Stock insuffisant pour {$produit->getLibelle()}. Disponible: {$produit->getStockActuel()}",
                    'vente_id' => null
                ];
            }

            $lignesVente[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'prix_unitaire' => $produit->getPrixVente(),
                'sous_total' => $quantite * $produit->getPrixVente()
            ];
            $montantTotal += $quantite * $produit->getPrixVente();
        }

        $soldeDisponible = $clientRepo->getSoldeDisponible($clientId);
        $montantRestant = $montantTotal - $montantVerse;

        if ($montantRestant > 0 && $montantRestant > $soldeDisponible) {
            return [
                'success' => false,
                'message' => "Limite de crédit dépassée. Solde disponible: " . number_format($soldeDisponible, 0, ',', ' ') . " FCFA, Reste dû: " . number_format($montantRestant, 0, ',', ' ') . " FCFA",
                'vente_id' => null
            ];
        }

        try {
            Database::beginTransaction($pdo);

            $numeroFacture = self::genererNumeroFacture($pdo);

            $vente = new Vente(
                $numeroFacture,
                $clientId,
                $utilisateurId,
                $montantTotal,
                $montantVerse,
                $modeReglement,
                'EN_ATTENTE',
                null
            );

            $venteId = self::insertVente($pdo, $vente);
            $vente->setId($venteId);

            foreach ($lignesVente as $ligneData) {
                $produit = $ligneData['produit'];
                $quantite = $ligneData['quantite'];
                $prixUnitaire = $ligneData['prix_unitaire'];

                $ligneVente = new LigneVente(
                    $venteId,
                    $produit->getId(),
                    $quantite,
                    $prixUnitaire
                );
                self::insertLigneVente($pdo, $ligneVente);

                $produitRepo->updateStock($produit->getId(), -$quantite);
            }

            if ($montantVerse > 0) {
                $clientRepo->updateSolde($clientId, $montantVerse);
            }

            $detteId = null;
            if ($montantRestant > 0) {
                $dette = new Dette(
                    self::genererRefDette($pdo),
                    $venteId,
                    $clientId,
                    $montantRestant,
                    0,
                    'NON_SOLDEE',
                    null
                );
                $detteId = self::insertDette($pdo, $dette);
            }

            if ($montantVerse >= $montantTotal) {
                $statut = 'PAYEE';
            } elseif ($montantVerse > 0) {
                $statut = 'PARTIELLE';
            } else {
                $statut = 'EN_ATTENTE';
            }
            self::updateVenteStatut($pdo, $venteId, $statut);

            Database::commit($pdo);

            return [
                'success' => true,
                'message' => 'Vente enregistrée avec succès',
                'vente_id' => $venteId,
                'dette_id' => $detteId,
                'numero_facture' => $numeroFacture,
                'montant_total' => $montantTotal,
                'montant_verse' => $montantVerse,
                'montant_restant' => $montantRestant
            ];

        } catch (Exception $e) {
            Database::rollBack($pdo);
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la vente: ' . $e->getMessage(),
                'vente_id' => null
            ];
        }
    }

    public static function calculerTotalPanier(array $panier): float
    {
        $produitRepo = new ProduitRepository();
        $total = 0;
        foreach ($panier as $produitId => $quantite) {
            $produit = $produitRepo->selectById((int)$produitId);
            if ($produit) {
                $total += $produit->getPrixVente() * $quantite;
            }
        }
        return $total;
    }

    public static function verifierStock(array $panier): array
    {
        $produitRepo = new ProduitRepository();
        $erreurs = [];
        foreach ($panier as $produitId => $quantite) {
            $produit = $produitRepo->selectById((int)$produitId);
            if (!$produit) {
                $erreurs[] = "Produit ID $produitId introuvable";
            } elseif ($produit->getStockActuel() < $quantite) {
                $erreurs[] = "Stock insuffisant pour {$produit->getLibelle()}. Disponible: {$produit->getStockActuel()}";
            }
        }
        return $erreurs;
    }

    private static function insertVente(PDO $pdo, Vente $vente): int
    {
        $sql = "INSERT INTO ventes (numero_facture, client_id, utilisateur_id, montant_total, montant_verse, mode_reglement, statut, date_echeance)
                VALUES (:numero_facture, :client_id, :utilisateur_id, :montant_total, :montant_verse, :mode_reglement, :statut, :date_echeance)";

        return Database::executeUpdate($pdo, $sql, [
            'numero_facture' => $vente->getNumeroFacture(),
            'client_id' => $vente->getClientId(),
            'utilisateur_id' => $vente->getUtilisateurId(),
            'montant_total' => $vente->getMontantTotal(),
            'montant_verse' => $vente->getMontantVerse(),
            'mode_reglement' => $vente->getModeReglement(),
            'statut' => $vente->getStatut(),
            'date_echeance' => $vente->getDateEcheance()
        ]);
    }

    private static function insertLigneVente(PDO $pdo, LigneVente $ligneVente): int
    {
        $sql = "INSERT INTO lignes_vente (vente_id, produit_id, quantite, prix_unitaire, sous_total)
                VALUES (:vente_id, :produit_id, :quantite, :prix_unitaire, :sous_total)";

        return Database::executeUpdate($pdo, $sql, [
            'vente_id' => $ligneVente->getVenteId(),
            'produit_id' => $ligneVente->getProduitId(),
            'quantite' => $ligneVente->getQuantite(),
            'prix_unitaire' => $ligneVente->getPrixUnitaire(),
            'sous_total' => $ligneVente->getSousTotal()
        ]);
    }

    private static function insertDette(PDO $pdo, Dette $dette): int
    {
        $sql = "INSERT INTO dettes (ref, vente_id, client_id, montant_initial, montant_verse, montant_restant, statut, date_echeance)
                VALUES (:ref, :vente_id, :client_id, :montant_initial, :montant_verse, :montant_restant, :statut, :date_echeance)";

        return Database::executeUpdate($pdo, $sql, [
            'ref' => $dette->getRef(),
            'vente_id' => $dette->getVenteId(),
            'client_id' => $dette->getClientId(),
            'montant_initial' => $dette->getMontantInitial(),
            'montant_verse' => $dette->getMontantVerse(),
            'montant_restant' => $dette->getMontantRestant(),
            'statut' => $dette->getStatut(),
            'date_echeance' => $dette->getDateEcheance()
        ]);
    }

    private static function updateVenteStatut(PDO $pdo, int $venteId, string $statut): bool
    {
        $sql = "UPDATE ventes SET statut = :statut WHERE id = :id";
        $result = Database::executeUpdate($pdo, $sql, [
            'id' => $venteId,
            'statut' => $statut
        ]);
        return $result > 0;
    }

    private static function genererNumeroFacture(PDO $pdo): string
    {
        $date = date('Ymd');
        $sql = "SELECT COUNT(*) as total FROM ventes WHERE numero_facture LIKE :prefix";
        $result = Database::executeQuery($pdo, $sql, ['prefix' => 'FAC-' . $date . '-%']);
        $count = $result ? (int)$result['total'] + 1 : 1;
        return 'FAC-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private static function genererRefDette(PDO $pdo): string
    {
        $date = date('Ymd');
        $sql = "SELECT COUNT(*) as total FROM dettes WHERE ref LIKE :prefix";
        $result = Database::executeQuery($pdo, $sql, ['prefix' => 'DT-' . $date . '-%']);
        $count = $result ? (int)$result['total'] + 1 : 1;
        return 'DT-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
