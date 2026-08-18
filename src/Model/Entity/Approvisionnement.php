<?php

require_once __DIR__ . '/Fournisseur.php';
require_once __DIR__ . '/Utilisateur.php';

class Approvisionnement
{
    private ?int $id;
    private string $reference_bl;
    private ?Fournisseur $fournisseur = null;
    private ?Utilisateur $utilisateur = null;
    private float $cout_total;
    private ?string $date_reception;
    private string $statut;
    private array $lignes = [];

    public function __construct(string $reference_bl,Fournisseur $fournisseur,?Utilisateur $utilisateur, float $cout_total, ?string $date_reception = null, string $statut = 'EN_ATTENTE', ?int $id = null)
    {
        $this-> id = $id;
        $this-> fournisseur = $fournisseur;
        $this-> utilisateur = $utilisateur;
        $this->reference_bl = $reference_bl;
        $this->cout_total = $cout_total;
        $this->date_reception = $date_reception;
        $this->statut = $statut;
    }

    public function getId(): ?int { 
        return $this->id; 
    }
    
    public function setId(?int $id): void { 
        $this->id = $id; 
    }

    public function getReferenceBl(): string { 
        return $this->reference_bl; 
    }

    public function getFournisseurId(): int { 
        return $this->fournisseur?->getId(); 
    }

    public function setFournisseurId(int $fournisseurId): void { 
        $this->fournisseurId = $fournisseurId; 
    }

    public function getFournisseur(): ?Fournisseur { 
        return $this->fournisseur; 
    }
    public function setFournisseur(?Fournisseur $fournisseur): void { 
        $this->fournisseur = $fournisseur; 
    }

    public function getUtilisateurId(): ?int { 
        return $this->utilisateurId?->getId(); 
    }

    public function setUtilisateurId(?int $utilisateurId): void { 
        $this->utilisateurId = $utilisateurId; 
    }

    public function getUtilisateur(): ?Utilisateur { 
        return $this->utilisateur; 
    }

    public function setUtilisateur(?Utilisateur $utilisateur): void { 
        $this->utilisateur = $utilisateur; 
    }

    public function getCoutTotal(): float { 
        return $this->cout_total; 
    }
    public function setCoutTotal(float $cout_total): void { 
        $this->cout_total = $cout_total; 
    }

    public function getDateReception(): ?string {
        return $this->date_reception; 
    }
    public function setDateReception(?string $date_reception): void {
        $this->date_reception = $date_reception; 
    }

    public function getStatut(): string {
        return $this->statut; 
    }

    public function setStatut(string $statut): void {
        $this->statut = $statut; 
    }

    public function isReceived(): bool {
        return $this->statut === 'RECU'; 
    }

    public function getLignes(): array {
        return $this->lignes; 
    }

    public function setLignes(array $lignes): void {
        $this->lignes = $lignes; 
        
    }

    public function addLigne($ligne): void
    {
        $this->lignes[] = $ligne;
        $this->cout_total += $ligne->getSousTotal();
    }
}
