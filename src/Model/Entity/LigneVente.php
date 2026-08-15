<?php

require_once __DIR__ . '/Produit.php';

class LigneVente
{
    private ?int $id;
    private int $venteId;
    private ?Vente $vente = null;
    private int $produitId;
    private ?Produit $produit = null;
    private int $quantite;
    private float $prix_unitaire;

    public function __construct(int $venteId, int $produitId, int $quantite, float $prix_unitaire, ?int $id = null)
    {
        $this->id = $id;
        $this->venteId = $venteId;
        $this->produitId = $produitId;
        $this->quantite = $quantite;
        $this->prix_unitaire = $prix_unitaire;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getVenteId(): int { return $this->venteId; }
    public function setVenteId(int $venteId): void { $this->venteId = $venteId; }

    public function getVente(): ?Vente { return $this->vente; }
    public function setVente(?Vente $vente): void { $this->vente = $vente; }

    public function getProduitId(): int { return $this->produitId; }
    public function setProduitId(int $produitId): void { $this->produitId = $produitId; }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): void { $this->produit = $produit; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): void { $this->quantite = $quantite; }

    public function getPrixUnitaire(): float { return $this->prix_unitaire; }
    public function setPrixUnitaire(float $prix_unitaire): void { $this->prix_unitaire = $prix_unitaire; }

    public function getSousTotal(): float { return $this->quantite * $this->prix_unitaire; }
}
