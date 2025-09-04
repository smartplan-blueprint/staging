<?php

namespace App\Entity\Merchant;

// src/Entity/MerchantDetails.php

use AllowDynamicProperties;
use App\Entity\Merchant;
use App\Repository\Merchant\MerchantDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: MerchantDetailsRepository::class)]
class MerchantDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

//    #[ORM\OneToOne(targetEntity: Merchant::class, inversedBy: "details")]
//    #[ORM\JoinColumn(name: "merchant_id", referencedColumnName: "id")]
//    private $merchant;


    #[ORM\OneToOne(inversedBy: 'merchantDetails', targetEntity: Merchant::class)]
    #[ORM\JoinColumn(name: 'merchant_id', referencedColumnName: 'id', nullable: false)]
    private ?Merchant $merchant = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $groupName = null;


    #[ORM\Column(type: 'string', length: 255)]
    private $contactName;

    #[ORM\Column(type: 'string', length: 255)]
    private $contactEmail;

    #[ORM\Column(type: 'string', length: 50)]
    private $contactPhone;

    public function __construct()
    {
        $this->dateRegistered = new \DateTime();
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
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param mixed $groupName
     */
    public function setGroupName($groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return mixed
     */
    public function getDateRegistered()
    {
        return $this->dateRegistered;
    }

    /**
     * @param mixed $dateRegistered
     */
    public function setDateRegistered($dateRegistered): void
    {
        $this->dateRegistered = $dateRegistered;
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
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * @param mixed $contactName
     */
    public function setContactName($contactName): void
    {
        $this->contactName = $contactName;
    }

    /**
     * @return mixed
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param mixed $contactEmail
     */
    public function setContactEmail($contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return mixed
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * @param mixed $contactPhone
     */
    public function setContactPhone($contactPhone): void
    {
        $this->contactPhone = $contactPhone;
    }
    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    public function setMerchant(?Merchant $merchant): self
    {
        $this->merchant = $merchant;
        return $this;
    }

    // ... getters and setters ...
}
