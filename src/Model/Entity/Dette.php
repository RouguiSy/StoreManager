<?php

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Vente.php';

class Dette
{
    private ?int $id;
    private string $ref;
    private Vente $vente ;
    private Client $client ;
    private float $montant_initial;
    private float $montant_verse;
    private string $statut;
    private ?string $date_echeance;
    private array $paiements = [];

    public function __construct(string $ref, Vente $vente, Client $client ,$clientId, float $montant_initial, float $montant_verse = 0, string $statut = 'NON_SOLDEE', ?string $date_echeance = null, ?int $id = null)
    {
        $this->id = $id;
        $this->ref = $ref;
        $this->vente = $vente;
        $this->client = $client;
        $this->montant_initial = $montant_initial;
        $this->montant_verse = $montant_verse;
        $this->statut = $statut;
        $this->date_echeance = $date_echeance;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getRef(): string { return $this->ref; }

    public function getVenteId(): int { return $this->venteId->getId(); }

    public function getVente(): ?Vente { return $this->vente; }
    public function setVente(?Vente $vente): void { $this->vente = $vente; }

    public function getClientId(): int { return $this->clientId->getId(); }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): void { $this->client = $client; }

    public function getMontantInitial(): float { return $this->montant_initial; }
    public function getMontantVerse(): float { return $this->montant_verse; }
    public function getMontantRestant(): float { return $this->montant_initial - $this->montant_verse; }

    public function isSold(): bool { return $this->montant_verse >= $this->montant_initial; }

    public function applyPayment(float $montant): void
    {
        $this->montant_verse += $montant;
        $this->updateStatut();
    }

    public function updateStatut(): void
    {
        $this->statut = $this->isSold() ? 'SOLDEE' : 'NON_SOLDEE';
    }

    public function getStatut(): string { return $this->statut; }
    public function getDateEcheance(): ?string { return $this->date_echeance; }
    public function setDateEcheance(?string $date_echeance): void { $this->date_echeance = $date_echeance; }

    public function getPaiements(): array { return $this->paiements; }
    public function setPaiements(array $paiements): void { $this->paiements = $paiements; }

    public function addPaiement($paiement): void
    {
        $this->paiements[] = $paiement;
        $this->applyPayment($paiement->getMontant());
    }
}
