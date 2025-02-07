<?php

namespace App\Entity;

use App\Repository\DataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataRepository::class)]
class Data
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 8, unique: true)]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    private ?string $encryptedValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getEncryptedValue(): ?string
    {
        return $this->encryptedValue;
    }

    public function setEncryptedValue(string $value): static
    {
        $this->encryptedValue = $value;

        return $this;
    }
}
