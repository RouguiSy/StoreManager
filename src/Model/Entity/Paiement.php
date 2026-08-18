<?php

require_once __DIR__ . '/Dette.php';
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/ModePaiement.php';

class Paiement
{
    private ?int $id;
    private ?Dette $dette = null;
    private Utilisateur $utilisateur ;
    private ModePaiement $modePaiement ;
    private float $montant;
    private ?string $notes;
    private ?string $reference;

    public function __construct(?Dette $dette, Utilisateur $utilisateur, ModePaiement $modePaiement, float $montant, ?string $notes = null, ?string $reference = null, ?int $id = null)
    {
        $this->id = $id;
        $this->dette = $dette;
        $this->utilisateur = $utilisateur;
        $this->modePaiement = $modePaiement;
        $this->montant = $montant;
        $this->notes = $notes;
        $this->reference = $reference;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getDetteId(): int { return $this->detteId->getId(); }

    public function getDette(): ?Dette { return $this->dette; }
    public function setDette(?Dette $dette): void { $this->dette = $dette; }

    public function getUtilisateurId(): ?int { return $this->utilisateurId->getId(); }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): void { $this->utilisateur = $utilisateur; }

    public function getModePaiementId(): int { return $this->modePaiementId->getId(); }

    public function getModePaiement(): ?ModePaiement { return $this->modePaiement; }
    public function setModePaiement(?ModePaiement $modePaiement): void { $this->modePaiement = $modePaiement; }

    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): void { $this->montant = $montant; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $reference): void { $this->reference = $reference; }
}
