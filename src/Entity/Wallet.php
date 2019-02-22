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
     * @ORM\Column(type="float", nullable=true)
     */
    private $balance;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $action;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAction;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cryptomonnaie", mappedBy="wallet")
     */
    private $cryptomonnaies;

    public function __construct()
    {
        $this->cryptomonnaies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getAction(): ?bool
    {
        return $this->action;
    }

    public function setAction(?bool $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDateAction(): ?\DateTimeInterface
    {
        return $this->dateAction;
    }

    public function setDateAction(?\DateTimeInterface $dateAction): self
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * @return Collection|Cryptomonnaie[]
     */
    public function getCryptomonnaies(): Collection
    {
        return $this->cryptomonnaies;
    }

    public function addCryptomonnaie(Cryptomonnaie $cryptomonnaie): self
    {
        if (!$this->cryptomonnaies->contains($cryptomonnaie)) {
            $this->cryptomonnaies[] = $cryptomonnaie;
            $cryptomonnaie->setWallet($this);
        }

        return $this;
    }

    public function removeCryptomonnaie(Cryptomonnaie $cryptomonnaie): self
    {
        if ($this->cryptomonnaies->contains($cryptomonnaie)) {
            $this->cryptomonnaies->removeElement($cryptomonnaie);
            // set the owning side to null (unless already changed)
            if ($cryptomonnaie->getWallet() === $this) {
                $cryptomonnaie->setWallet(null);
            }
        }

        return $this;
    }
}
