<?php

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Utilisateur.php';

class Vente
{
    private ?int $id;
    private string $numero_facture;
    private Client $client ;
    private Utilisateur $utilisateur ;
    private float $montant_total;
    private float $montant_verse;
    private ?string $mode_reglement;
    private string $statut;
    private ?string $date_echeance;
    private array $lignes = [];
    private ?Dette $dette = null;

    public function __construct(string $numero_facture, Client $client, Utilisateur $utilisateur, float $montant_total, float $montant_verse = 0, ?string $mode_reglement = null, string $statut = 'EN_ATTENTE', ?string $date_echeance = null, ?int $id = null)
    {
        $this->id = $id;
        $this->numero_facture = $numero_facture;
        $this->client = $client;
        $this->utilisateur = $utilisateur;
        $this->montant_total = $montant_total;
        $this->montant_verse = $montant_verse;
        $this->mode_reglement = $mode_reglement;
        $this->statut = $statut;
        $this->date_echeance = $date_echeance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNumeroFacture(): string
    {
        return $this->numero_facture;
    }

    public function getClientId(): int
    {
        return $this->clientId->getId();
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateurId->getId();
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): void
    {
        $this->utilisateur = $utilisateur;
    }

    public function getMontantTotal(): float
    {
        return $this->montant_total;
    }

    public function setMontantTotal(float $montant_total): void
    {
        $this->montant_total = $montant_total;
    }

    public function getMontantVerse(): float
    {
        return $this->montant_verse;
    }

    public function setMontantVerse(float $montant_verse): void
    {
        $this->montant_verse = $montant_verse;
        $this->updateStatut();
    }

    public function getMontantRestant(): float
    {
        return $this->montant_total - $this->montant_verse;
    }

    public function isFullyPaid(): bool
    {
        return $this->montant_verse >= $this->montant_total;
    }

    public function getModeReglement(): ?string
    {
        return $this->mode_reglement;
    }

    public function setModeReglement(?string $mode_reglement): void
    {
        $this->mode_reglement = $mode_reglement;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function updateStatut(): void
    {
        if ($this->isFullyPaid()) {
            $this->statut = 'PAYEE';
        } elseif ($this->montant_verse > 0) {
            $this->statut = 'PARTIELLE';
        } else {
            $this->statut = 'EN_ATTENTE';
        }
    }

    public function getDateEcheance(): ?string
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(?string $date_echeance): void
    {
        $this->date_echeance = $date_echeance;
    }

    public function getLignes(): array
    {
        return $this->lignes;
    }

    public function setLignes(array $lignes): void
    {
        $this->lignes = $lignes;
    }

    public function addLigne($ligne): void
    {
        $this->lignes[] = $ligne;
        $this->montant_total += $ligne->getSousTotal();
    }

    public function getDette(): ?Dette
    {
        return $this->dette;
    }

    public function setDette(?Dette $dette): void
    {
        $this->dette = $dette;
    }

    public function hasDette(): bool
    {
        return $this->dette !== null;
    }
}
