<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WalletRepository")
 */
class Wallet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="wallets")
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cryptomonney", inversedBy="wallets")
     */
    private $cryptomonney;

    public function __construct()
    {
        $this->cryptomonney = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection|Cryptomonney[]
     */
    public function getCryptomonney()
    {
        return $this->cryptomonney;
    }

    public function addCryptomonney(Cryptomonney $cryptomonney): self
    {
        if (!$this->cryptomonney->contains($cryptomonney)) {
            $this->cryptomonney[] = $cryptomonney;
        }

        return $this;
    }

    public function removeCryptomonney(Cryptomonney $cryptomonney): self
    {
        if ($this->cryptomonney->contains($cryptomonney)) {
            $this->cryptomonney->removeElement($cryptomonney);
        }

        return $this;
    }
}
