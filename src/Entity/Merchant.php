<?php

namespace App\Entity;

use App\Entity\Financials\Reversals;
use App\Entity\LimitsAndDocs\CreditLimit;
use App\Entity\LimitsAndDocs\Provider;
use App\Entity\Merchant\MerchantDetails;
use App\Entity\Merchant\OutletDetails;
use App\Entity\Merchant\PortalUserDetails;
use App\Entity\Reports\Outlet\ReportsTransaction;
use App\Repository\MerchantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MerchantRepository::class)]
class Merchant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $code = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $registrationDate = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $clientId = null;

    #[ORM\OneToOne(mappedBy: 'merchant', targetEntity: MerchantDetails::class, cascade: ['persist', 'remove'])]
    private ?MerchantDetails $merchantDetails = null;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'merchant', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['date' => 'DESC'])]
    private Collection $transaction;

    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: ReportsTransaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(targetEntity: PortalUserDetails::class, mappedBy: 'merchant', cascade: ['persist', 'remove'])]
    private Collection $portalUsers;

    #[ORM\OneToMany(targetEntity: OutletDetails::class, mappedBy: 'merchant', cascade: ['persist', 'remove'])]
    private Collection $outletDetails;

    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: Reversals::class)]
    private Collection $reversals;

    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: CreditLimit::class, cascade: ['persist', 'remove'])]
    private Collection $creditLimits;

    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: Provider::class, cascade: ['persist', 'remove'])]
    private Collection $providers;

    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private Collection $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commission", mappedBy="merchant")
     */
    private Collection $agreementCommissions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agreement", mappedBy="merchant")
     */
    private Collection $agreements;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->portalUsers = new ArrayCollection();
        $this->outletDetails = new ArrayCollection();
        $this->reversals = new ArrayCollection();
        $this->creditLimits = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->agreementCommissions = new ArrayCollection();
        $this->agreements = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    // ───────────────────────────────
    // Basic fields
    // ───────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?\DateTimeInterface $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    // ───────────────────────────────
    // MerchantDetails (OneToOne)
    // ───────────────────────────────

    public function getMerchantDetails(): ?MerchantDetails
    {
        return $this->merchantDetails;
    }

    public function setMerchantDetails(?MerchantDetails $merchantDetails): self
    {
        if ($merchantDetails === null && $this->merchantDetails !== null) {
            $this->merchantDetails->setMerchant(null);
        }

        if ($merchantDetails !== null && $merchantDetails->getMerchant() !== $this) {
            $merchantDetails->setMerchant($this);
        }

        $this->merchantDetails = $merchantDetails;
        return $this;
    }

    // ───────────────────────────────
    // Relations
    // ───────────────────────────────

    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function setTransaction(Collection $transaction): void
    {
        $this->transaction = $transaction;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(ReportsTransaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setMerchant($this);
        }
        return $this;
    }

    public function removeTransaction(ReportsTransaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            if ($transaction->getMerchant() === $this) {
                $transaction->setMerchant(null);
            }
        }
        return $this;
    }

    public function getPortalUsers(): Collection
    {
        return $this->portalUsers;
    }

    public function addPortalUser(PortalUserDetails $portalUser): self
    {
        if (!$this->portalUsers->contains($portalUser)) {
            $this->portalUsers->add($portalUser);
            $portalUser->setMerchant($this);
        }
        return $this;
    }

    public function removePortalUser(PortalUserDetails $portalUser): self
    {
        if ($this->portalUsers->removeElement($portalUser)) {
            if ($portalUser->getMerchant() === $this) {
                $portalUser->setMerchant(null);
            }
        }
        return $this;
    }

    public function getOutletDetails(): Collection
    {
        return $this->outletDetails;
    }

    public function getReversals(): Collection
    {
        return $this->reversals;
    }

    public function getCreditLimits(): Collection
    {
        return $this->creditLimits;
    }

    public function addCreditLimit(CreditLimit $creditLimit): self
    {
        if (!$this->creditLimits->contains($creditLimit)) {
            $this->creditLimits->add($creditLimit);
            $creditLimit->setMerchant($this);
        }
        return $this;
    }

    public function removeCreditLimit(CreditLimit $creditLimit): self
    {
        if ($this->creditLimits->removeElement($creditLimit)) {
            if ($creditLimit->getMerchant() === $this) {
                $creditLimit->setMerchant(null);
            }
        }
        return $this;
    }

    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
            $provider->setMerchant($this);
        }
        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->removeElement($provider)) {
            if ($provider->getMerchant() === $this) {
                $provider->setMerchant(null);
            }
        }
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setMerchant($this);
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            if ($user->getMerchant() === $this) {
                $user->setMerchant(null);
            }
        }
        return $this;
    }

    public function getAgreementCommissions(): Collection
    {
        return $this->agreementCommissions;
    }

    public function setAgreementCommissions(Collection $agreementCommissions): void
    {
        $this->agreementCommissions = $agreementCommissions;
    }

    public function getAgreements(): Collection
    {
        return $this->agreements;
    }

    public function setAgreements(Collection $agreements): void
    {
        $this->agreements = $agreements;
    }
}
