<?php

class Client
{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $telephone;
    private ?string $email;
    private float $limite_credit;
    private float $solde_actuel;
    private array $dettes = [];
    private array $ventes = [];

    public function __construct(string $nom, string $prenom, string $telephone, ?string $email = null, float $limite_credit = 0, float $solde_actuel = 0, ?int $id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->limite_credit = $limite_credit;
        $this->solde_actuel = $solde_actuel;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getLimiteCredit(): float
    {
        return $this->limite_credit;
    }

    public function setLimiteCredit(float $limite_credit): void
    {
        $this->limite_credit = $limite_credit;
    }

    public function getSoldeActuel(): float
    {
        return $this->solde_actuel;
    }

    public function setSoldeActuel(float $solde_actuel): void
    {
        $this->solde_actuel = $solde_actuel;
    }

    public function getCreditRestant(): float
    {
        return $this->limite_credit - $this->solde_actuel;
    }

    public function peutAcheter(float $montant): bool
    {
        return ($this->solde_actuel + $montant) <= $this->limite_credit;
    }

    public function updateSolde(float $montant): void
    {
        $this->solde_actuel += $montant;
    }

    public function getDettes(): array
    {
        return $this->dettes;
    }

    public function setDettes(array $dettes): void
    {
        $this->dettes = $dettes;
    }

    public function addDette($dette): void
    {
        $this->dettes[] = $dette;
    }

    public function getVentes(): array
    {
        return $this->ventes;
    }

    public function setVentes(array $ventes): void
    {
        $this->ventes = $ventes;
    }

    public function addVente($vente): void
    {
        $this->ventes[] = $vente;
    }
}
