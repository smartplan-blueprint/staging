<?php

// src/Entity/Merchant.php
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
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: MerchantRepository::class)]
class Merchant
{

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'merchant', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy( ['date' => 'DESC' ])]
    #[ORM\JoinColumn(nullable: false)]
    private $transaction;

    #[ORM\OneToOne(targetEntity: MerchantDetails::class, mappedBy: 'merchant', cascade: ['persist', 'remove'])]
    private $details;

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

    #[ORM\OneToOne(mappedBy: 'merchant', targetEntity: MerchantDetails::class, cascade: ['persist', 'remove'])]
    private ?MerchantDetails $merchantDetails = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 50)]
    private $code;

    #[ORM\Column(type: 'date')]
    private $registrationDate;

    #[ORM\Column(type: 'boolean')]
    private $isActive;

    #[ORM\Column(type: 'string', length: 50)]
    private $clientId;

    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: ReportsTransaction::class)]
    private Collection $transactions;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commission", mappedBy="merchant")
     */
    private $agreementCommissions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agreement", mappedBy="merchant")
     */
    private $agreements;
    #[ORM\OneToMany(mappedBy: 'merchant', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private Collection $users;

    // Getters and setters...
    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->details = new MerchantDetails();
        $this->portalUsers = new ArrayCollection();
        $this->outletDetails = new ArrayCollection();
        $this->reversals = new ArrayCollection();
        $this->creditLimits = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->agreementCommissions = new ArrayCollection();
        $this->agreements = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->users = new ArrayCollection();

    }


    public function getAgreementCommissions(): ArrayCollection
    {
        return $this->agreementCommissions;
    }

    public function setAgreementCommissions(ArrayCollection $agreementCommissions): void
    {
        $this->agreementCommissions = $agreementCommissions;
    }

    public function getAgreements(): ArrayCollection
    {
        return $this->agreements;
    }

    public function setAgreements(ArrayCollection $agreements): void
    {
        $this->agreements = $agreements;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @param mixed $registrationDate
     */
    public function setRegistrationDate($registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param mixed $transaction
     */
    public function setTransaction($transaction): void
    {
        $this->transaction = $transaction;
    }


    public function getDetails(): ?MerchantDetails
    {
        return $this->details;
    }

    public function setDetails(?MerchantDetails $details): self
    {
        // unset the owning side of the relation if necessary
        if ($details === null && $this->details !== null) {
            $this->details->setMerchant(null);
        }

        // set the owning side of the relation if necessary
        if ($details !== null && $details->getMerchant() !== $this) {
            $details->setMerchant($this);
        }

        $this->details = $details;
        return $this;
    }

    public function getPortalUsers(): Collection
    {
        return $this->portalUsers;
    }

    public function getOutletDetails(): Collection
    {
        return $this->outletDetails;
    }

    public function getReversals(): Collection
    {
        return $this->reversals;
    }
    public function addPortalUser(PortalUserDetails $portalUser): self
    {
        if (!$this->portalUsers->contains($portalUser)) {
            $this->portalUsers[] = $portalUser;
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

    /**
     * @return Collection<int, CreditLimit>
     */
    public function getCreditLimits(): Collection
    {
        return $this->creditLimits;
    }

    public function addCreditLimit(CreditLimit $creditLimit): static
    {
        if (!$this->creditLimits->contains($creditLimit)) {
            $this->creditLimits->add($creditLimit);
            $creditLimit->setMerchant($this);
        }

        return $this;
    }

    public function removeCreditLimit(CreditLimit $creditLimit): static
    {
        if ($this->creditLimits->removeElement($creditLimit)) {
            // set the owning side to null (unless already changed)
            if ($creditLimit->getMerchant() === $this) {
                $creditLimit->setMerchant(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Provider>
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): static
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
            $provider->setMerchant($this);
        }
        return $this;
    }

    public function removeProvider(Provider $provider): static
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getMerchant() === $this) {
                $provider->setMerchant(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ReportsTransaction>
     */
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
            // set the owning side to null (unless already changed)
            if ($transaction->getMerchant() === $this) {
                $transaction->setMerchant(null);
            }
        }
        return $this;
    }

    // Add this method to your Merchant class
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


    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setMerchant($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getMerchant() === $this) {
                $user->setMerchant(null);
            }
        }

        return $this;
    }

}
