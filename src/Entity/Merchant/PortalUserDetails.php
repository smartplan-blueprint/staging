<?php

namespace App\Entity\Merchant;
use AllowDynamicProperties;
use App\Entity\Merchant;
use App\Repository\Merchant\PortalUserRepository;
use App\Repository\MerchantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: PortalUserRepository::class)]
class PortalUserDetails implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Merchant::class, inversedBy: 'portalUsers')]
    #[ORM\JoinColumn(name: "merchant_id", referencedColumnName: "id")]
    private Merchant $merchant;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCreated;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
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
    public function setId($id): self
    {
        $this->id = $id;
        return $this;

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
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }






    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null; // Not needed if using modern hashing
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }


}
