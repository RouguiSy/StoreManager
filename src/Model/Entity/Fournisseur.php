<?php

class Fournisseur
{
    private ?int $id;
    private string $nom;
    private ?string $email;
    private string $telephone;
    private ?string $adresse;
    private array $approvisionnements = [];

    public function __construct(string $nom, ?string $email, string $telephone, ?string $adresse = null, ?int $id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getContact(): string
    {
        return $this->nom . ' - Tél: ' . $this->telephone;
    }

    public function getApprovisionnements(): array
    {
        return $this->approvisionnements;
    }

    public function setApprovisionnements(array $approvisionnements): void
    {
        $this->approvisionnements = $approvisionnements;
    }

    public function addApprovisionnement($approvisionnement): void
    {
        $this->approvisionnements[] = $approvisionnement;
    }
}
