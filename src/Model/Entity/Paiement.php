<?php

require_once __DIR__ . '/Dette.php';
require_once __DIR__ . '/Utilisateur.php';
require_once __DIR__ . '/ModePaiement.php';

class Paiement
{
    private ?int $id;
    private int $detteId;
    private ?Dette $dette = null;
    private ?int $utilisateurId;
    private ?Utilisateur $utilisateur = null;
    private int $modePaiementId;
    private ?ModePaiement $modePaiement = null;
    private float $montant;
    private ?string $notes;
    private ?string $reference;

    public function __construct(int $detteId, ?int $utilisateurId, int $modePaiementId, float $montant, ?string $notes = null, ?string $reference = null, ?int $id = null)
    {
        $this->id = $id;
        $this->detteId = $detteId;
        $this->utilisateurId = $utilisateurId;
        $this->modePaiementId = $modePaiementId;
        $this->montant = $montant;
        $this->notes = $notes;
        $this->reference = $reference;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getDetteId(): int { return $this->detteId; }
    public function setDetteId(int $detteId): void { $this->detteId = $detteId; }

    public function getDette(): ?Dette { return $this->dette; }
    public function setDette(?Dette $dette): void { $this->dette = $dette; }

    public function getUtilisateurId(): ?int { return $this->utilisateurId; }
    public function setUtilisateurId(?int $utilisateurId): void { $this->utilisateurId = $utilisateurId; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): void { $this->utilisateur = $utilisateur; }

    public function getModePaiementId(): int { return $this->modePaiementId; }
    public function setModePaiementId(int $modePaiementId): void { $this->modePaiementId = $modePaiementId; }

    public function getModePaiement(): ?ModePaiement { return $this->modePaiement; }
    public function setModePaiement(?ModePaiement $modePaiement): void { $this->modePaiement = $modePaiement; }

    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): void { $this->montant = $montant; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $reference): void { $this->reference = $reference; }
}
