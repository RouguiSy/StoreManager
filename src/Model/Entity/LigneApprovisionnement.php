<?php

require_once __DIR__ . '/Produit.php';

class LigneApprovisionnement
{
    private ?int $id;
    private int $approvisionnementId;
    private ?Approvisionnement $approvisionnement = null;
    private int $produitId;
    private ?Produit $produit = null;
    private int $quantite_appro;
    private int $quantite_recue;
    private float $prix_achat;

    public function __construct(int $approvisionnementId, int $produitId, int $quantite_appro, float $prix_achat, int $quantite_recue = 0, ?int $id = null)
    {
        $this->id = $id;
        $this->approvisionnementId = $approvisionnementId;
        $this->produitId = $produitId;
        $this->quantite_appro = $quantite_appro;
        $this->quantite_recue = $quantite_recue;
        $this->prix_achat = $prix_achat;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getApprovisionnementId(): int { return $this->approvisionnementId; }
    public function setApprovisionnementId(int $approvisionnementId): void { $this->approvisionnementId = $approvisionnementId; }

    public function getApprovisionnement(): ?Approvisionnement { return $this->approvisionnement; }
    public function setApprovisionnement(?Approvisionnement $approvisionnement): void { $this->approvisionnement = $approvisionnement; }

    public function getProduitId(): int { return $this->produitId; }
    public function setProduitId(int $produitId): void { $this->produitId = $produitId; }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): void { $this->produit = $produit; }

    public function getQuantiteAppro(): int { return $this->quantite_appro; }
    public function setQuantiteAppro(int $quantite_appro): void { $this->quantite_appro = $quantite_appro; }

    public function getQuantiteRecue(): int { return $this->quantite_recue; }
    public function setQuantiteRecue(int $quantite_recue): void { $this->quantite_recue = $quantite_recue; }

    public function getQuantiteManquante(): int { return $this->quantite_appro - $this->quantite_recue; }

    public function getPrixAchat(): float { return $this->prix_achat; }
    public function setPrixAchat(float $prix_achat): void { $this->prix_achat = $prix_achat; }

    public function getSousTotal(): float { return $this->quantite_appro * $this->prix_achat; }
    public function getSousTotalRecu(): float { return $this->quantite_recue * $this->prix_achat; }
}
