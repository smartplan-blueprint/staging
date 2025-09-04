<?php
//
//namespace App\Entity;
//
//
//// src/Entity/User.php
//namespace App\Entity;
//
//use App\Repository\UserRepository;
//use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
//use Symfony\Component\Security\Core\User\UserInterface;
//
//#[ORM\Entity(repositoryClass: UserRepository::class)]
//class User implements UserInterface, PasswordAuthenticatedUserInterface
//{
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column]
//    private ?int $id = null;
//
//    #[ORM\Column(length: 180, unique: true)]
//    private ?string $email = null;
//
//    #[ORM\Column]
//    private array $roles = [];
//
//    #[ORM\Column]
//    private ?string $password = null;
//
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
//
//    public function getEmail(): ?string
//    {
//        return $this->email;
//    }
//
//    public function setEmail(string $email): self
//    {
//        $this->email = $email;
//        return $this;
//    }
//
//    public function getUserIdentifier(): string
//    {
//        return (string) $this->email;
//    }
//
//    public function getRoles(): array
//    {
//        $roles = $this->roles;
//        $roles[] = 'ROLE_USER';
//
//        return array_unique($roles);
//    }
//
//    public function setRoles(array $roles): self
//    {
//        $this->roles = $roles;
//        return $this;
//    }
//
//    public function getPassword(): string
//    {
//        return $this->password;
//    }
//
//    public function setPassword(string $password): self
//    {
//        $this->password = $password;
//        return $this;
//    }
//
//    public function eraseCredentials(): void
//    {
//        // If you store any temporary, sensitive data on the user, clear it here
//        // $this->plainPassword = null;
//    }
//}


// src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'portal_user_details')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date_created;

    #[ORM\ManyToOne(targetEntity: Merchant::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private Merchant $merchant;


    private $isTempPassword = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordChangedAt;


    // Transient properties (not stored in DB)
    private ?string $firstName = null;
    private ?string $lastName = null;

    public function __construct()
    {
        $this->date_created = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getFirstName(): string
    {
        if ($this->firstName === null) {
            $this->splitName();
        }
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        $this->combineName();
        return $this;
    }

    public function getLastName(): string
    {
        if ($this->lastName === null) {
            $this->splitName();
        }
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        $this->combineName();
        return $this;
    }

    private function splitName(): void
    {
        $parts = explode(' ', $this->name, 2);
        $this->firstName = $parts[0] ?? '';
        $this->lastName = $parts[1] ?? '';
    }

    private function combineName(): void
    {
        $this->name = trim($this->firstName . ' ' . $this->lastName);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
//        $roles = $this->roles;
//        // guarantee every user at least has ROLE_USER
//        $roles[] = 'USER';
//
//        return array_unique($roles);

        $roles = $this->roles;
        // Ensure all roles start with ROLE_ and uppercase
        $roles = array_map(function($role) {
            return str_starts_with($role, 'ROLE_') ? $role : 'ROLE_' . strtoupper($role);
        }, $roles);

        // guarantee every user at least has ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getDate_created(): \DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDate_created(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;
        return $this;
    }

    public function getMerchant(): Merchant
    {
        return $this->merchant;
    }

    public function setMerchant(Merchant $merchant): self
    {
        $this->merchant = $merchant;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getIsTempPassword(): bool
    {
        return $this->isTempPassword;
    }

    public function setIsTempPassword(bool $isTempPassword): self
    {
        $this->isTempPassword = $isTempPassword;
        return $this;
    }

    public function getPasswordChangedAt(): ?\DateTimeInterface
    {
        return $this->passwordChangedAt;
    }

    public function setPasswordChangedAt(?\DateTimeInterface $passwordChangedAt): self
    {
        $this->passwordChangedAt = $passwordChangedAt;
        return $this;
    }

    /**
     * Check if user needs password reset (using temp password for more than 7 days)
     */
    public function needsPasswordReset(): bool
    {
        return $this->isTempPassword &&
            $this->getDate_created() &&
            $this->getDate_created()->diff(new \DateTime())->days > 7;
    }


}

//// src/Entity/User.php
//
//namespace App\Entity;
//
//use AllowDynamicProperties;
//use App\Repository\UserRepository;
//use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
//use Symfony\Component\Security\Core\User\UserInterface;
//
//#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: UserRepository::class)]
//#[ORM\Table(name: 'portal_user_details')]
//class User implements UserInterface, PasswordAuthenticatedUserInterface
//{
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column(type: 'integer')]
//    private ?int $id = null;
//
//    #[ORM\Column(type: 'string', length: 50)]
//    private string $firstName;
//
//    #[ORM\Column(type: 'string', length: 50)]
//    private string $lastName;
//
//    #[ORM\Column(type: 'string', length: 180, unique: true)]
//    private string $email;
//
//    #[ORM\Column(type: 'json')]
//    private array $roles = [];
//
//    #[ORM\Column(type: 'string')]
//    private string $password;
//
//    #[ORM\Column(type: 'boolean')]
//    private bool $isActive = true;
//
//    #[ORM\Column(type: 'datetime')]
//    private \DateTimeInterface $createdAt;
//
//    #[ORM\ManyToOne(targetEntity: Merchant::class, inversedBy: 'users')]
//    #[ORM\JoinColumn(nullable: false)]
//    private Merchant $merchant;
//
//    public function __construct()
//    {
//        $this->createdAt = new \DateTime();
//    }
//
//    public function getId(): ?int
//    {
//        return $this->id;
//    }
//
//    public function getFirstName(): string
//    {
//        return $this->firstName;
//    }
//
//    public function setFirstName(string $firstName): self
//    {
//        $this->firstName = $firstName;
//        return $this;
//    }
//
//    public function getLastName(): string
//    {
//        return $this->lastName;
//    }
//
//    public function setLastName(string $lastName): self
//    {
//        $this->lastName = $lastName;
//        return $this;
//    }
//
//    public function getFullName(): string
//    {
//        return $this->firstName . ' ' . $this->lastName;
//    }
//
//    public function getEmail(): string
//    {
//        return $this->email;
//    }
//
//    public function setEmail(string $email): self
//    {
//        $this->email = $email;
//        return $this;
//    }
//
//    /**
//     * A visual identifier that represents this user.
//     *
//     * @see UserInterface
//     */
//    public function getUserIdentifier(): string
//    {
//        return $this->email;
//    }
//
//    /**
//     * @see UserInterface
//     */
//    public function getRoles(): array
//    {
//        $roles = $this->roles;
//        // guarantee every user at least has ROLE_USER
//        $roles[] = 'User';
//
//        return array_unique($roles);
//    }
//
//    public function setRoles(array $roles): self
//    {
//        $this->roles = $roles;
//        return $this;
//    }
//
//    public function addRole(string $role): self
//    {
//        if (!in_array($role, $this->roles, true)) {
//            $this->roles[] = $role;
//        }
//        return $this;
//    }
//
//    /**
//     * @see PasswordAuthenticatedUserInterface
//     */
//    public function getPassword(): string
//    {
//        return $this->password;
//    }
//
//    public function setPassword(string $password): self
//    {
//        $this->password = $password;
//        return $this;
//    }
//
//    public function isActive(): bool
//    {
//        return $this->isActive;
//    }
//
//    public function setIsActive(bool $isActive): self
//    {
//        $this->isActive = $isActive;
//        return $this;
//    }
//
//    public function getCreatedAt(): \DateTimeInterface
//    {
//        return $this->createdAt;
//    }
//
//    public function setCreatedAt(\DateTimeInterface $createdAt): self
//    {
//        $this->createdAt = $createdAt;
//        return $this;
//    }
//
//    public function getMerchant(): Merchant
//    {
//        return $this->merchant;
//    }
//
//    public function setMerchant(Merchant $merchant): self
//    {
//        $this->merchant = $merchant;
//        return $this;
//    }
//
//    /**
//     * @see UserInterface
//     */
//    public function eraseCredentials(): void
//    {
//        // If you store any temporary, sensitive data on the user, clear it here
//       //  $this->plainPassword = null;
//    }
//
//    public function __toString(): string
//    {
//        return $this->getFullName();
//    }
//}
