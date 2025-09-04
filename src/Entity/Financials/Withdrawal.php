<?php

namespace App\Entity\Financials;

use App\Entity\Merchant;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'withdrawals')]
class Withdrawal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Merchant::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Merchant $merchant = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $reference_number = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'pending';

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $method = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $processed_at = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    public function setMerchant(?Merchant $merchant): void
    {
        $this->merchant = $merchant;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): void
    {
        $this->amount = $amount;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


    // ... getters and setters ...

    public function getReferenceNumber(): ?string
    {
        return $this->reference_number;
    }

    public function setReferenceNumber(?string $reference_number): self
    {
        $this->reference_number = $reference_number;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getProcessedAt(): ?\DateTimeInterface
    {
        return $this->processed_at;
    }

    public function setProcessedAt(?\DateTimeInterface $processed_at): self
    {
        $this->processed_at = $processed_at;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }
}
