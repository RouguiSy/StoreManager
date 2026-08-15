<?php

require_once __DIR__ . '/Fournisseur.php';

class Produit
{
    private ?int $id;
    private string $code;
    private string $libelle;
    private ?string $categorie;
    private float $prix_vente;
    private float $cout_achat;
    private int $stock_initial;
    private int $stock_actuel;
    private int $seuil_alerte;
    private int $fournisseurId;
    private ?Fournisseur $fournisseur = null;
    private array $lignesVente = [];
    private array $lignesAppro = [];

    public function __construct(string $code, string $libelle, ?string $categorie, float $prix_vente, float $cout_achat, int $fournisseurId, int $stock_initial = 0, int $stock_actuel = 0, int $seuil_alerte = 5, ?int $id = null)
    {
        $this->id = $id;
        $this->code = $code;
        $this->libelle = $libelle;
        $this->categorie = $categorie;
        $this->prix_vente = $prix_vente;
        $this->cout_achat = $cout_achat;
        $this->fournisseurId = $fournisseurId;
        $this->stock_initial = $stock_initial;
        $this->stock_actuel = $stock_actuel;
        $this->seuil_alerte = $seuil_alerte;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): void
    {
        $this->categorie = $categorie;
    }

    public function getPrixVente(): float
    {
        return $this->prix_vente;
    }

    public function setPrixVente(float $prix_vente): void
    {
        $this->prix_vente = $prix_vente;
    }

    public function getCoutAchat(): float
    {
        return $this->cout_achat;
    }

    public function setCoutAchat(float $cout_achat): void
    {
        $this->cout_achat = $cout_achat;
    }

    public function getStockInitial(): int
    {
        return $this->stock_initial;
    }

    public function getStockActuel(): int
    {
        return $this->stock_actuel;
    }

    public function setStockActuel(int $stock_actuel): void
    {
        $this->stock_actuel = $stock_actuel;
    }

    public function updateStock(int $quantite): void
    {
        $this->stock_actuel += $quantite;
    }

    public function getSeuilAlerte(): int
    {
        return $this->seuil_alerte;
    }

    public function setSeuilAlerte(int $seuil_alerte): void
    {
        $this->seuil_alerte = $seuil_alerte;
    }

    public function isStockLow(): bool
    {
        return $this->stock_actuel <= $this->seuil_alerte;
    }

    public function getValeurStock(): float
    {
        return $this->stock_actuel * $this->prix_vente;
    }

    public function getMarge(): float
    {
        return $this->prix_vente - $this->cout_achat;
    }

    public function getTauxMarge(): float
    {
        if ($this->cout_achat == 0) return 0;
        return ($this->getMarge() / $this->cout_achat) * 100;
    }

    public function getFournisseurId(): int
    {
        return $this->fournisseurId;
    }

    public function setFournisseurId(int $fournisseurId): void
    {
        $this->fournisseurId = $fournisseurId;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): void
    {
        $this->fournisseur = $fournisseur;
    }

    public function getLignesVente(): array
    {
        return $this->lignesVente;
    }

    public function setLignesVente(array $lignesVente): void
    {
        $this->lignesVente = $lignesVente;
    }

    public function addLigneVente($ligneVente): void
    {
        $this->lignesVente[] = $ligneVente;
    }

    public function getLignesAppro(): array
    {
        return $this->lignesAppro;
    }

    public function setLignesAppro(array $lignesAppro): void
    {
        $this->lignesAppro = $lignesAppro;
    }

    public function addLigneAppro($ligneAppro): void
    {
        $this->lignesAppro[] = $ligneAppro;
    }
}
