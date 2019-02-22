<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CryptomonnaieRepository")
 */
class Cryptomonnaie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $actualCurrency;

    /**
     * @ORM\Column(type="float")
     */
    private $percentageCurrencyOfDay;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCurrent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wallet", inversedBy="cryptomonnaies")
     */
    private $wallet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getActualCurrency(): ?float
    {
        return $this->actualCurrency;
    }

    public function setActualCurrency(float $actualCurrency): self
    {
        $this->actualCurrency = $actualCurrency;

        return $this;
    }

    public function getPercentageCurrencyOfDay(): ?float
    {
        return $this->percentageCurrencyOfDay;
    }

    public function setPercentageCurrencyOfDay(float $percentageCurrencyOfDay): self
    {
        $this->percentageCurrencyOfDay = $percentageCurrencyOfDay;

        return $this;
    }

    public function getDateCurrent(): ?\DateTimeInterface
    {
        return $this->dateCurrent;
    }

    public function setDateCurrent(\DateTimeInterface $dateCurrent): self
    {
        $this->dateCurrent = $dateCurrent;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }
}
