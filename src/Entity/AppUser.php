<?php

namespace App\Entity;

use App\Repository\AppUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppUserRepository::class)]
#[UniqueEntity(fields: "email", message: "Email already used")]
class AppUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups('users')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('users')]
    #[ORM\Column(length: 255)]
    #[Assert\Email(
        message: "Email {{ value }} are not a valid email")
    ]
    private ?string $email = null;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(
        message: "The password cannot be blank."
    )]
    #[Assert\Length(
        min: 8,
        max: 128,
        minMessage: "The password must be at least {{ limit }} characters long.",
        maxMessage: "The password cannot be longer than {{ limit }} characters."
    )]
    #[Assert\Regex(
        pattern: "/[A-Z]/",
        message: "The password must contain at least one uppercase letter."
    )]
    #[Assert\Regex(
        pattern: "/[a-z]/",
        message: "The password must contain at least one lowercase letter."
    )]
    #[Assert\Regex(
        pattern: "/[0-9]/",
        message: "The password must contain at least one number."
    )]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[Groups('users')]
    #[ORM\Column]
    private ?bool $is_admin = null;

    #[Groups('users')]
    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $owned_albums;

    #[Groups('users')]
    #[ORM\ManyToMany(targetEntity: Album::class, inversedBy: 'shared_to')]
    private Collection $shared_albums;

    public function __construct()
    {
        $this->owned_albums = new ArrayCollection();
        $this->shared_albums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
        return (string)$this->email;
    }

    /**
     * @return list<string>
     * @see UserInterface
     *
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function isAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): static
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getOwnedAlbums(): Collection
    {
        return $this->owned_albums;
    }

    public function addOwnedAlbum(Album $ownedAlbum): static
    {
        if (!$this->owned_albums->contains($ownedAlbum)) {
            $this->owned_albums->add($ownedAlbum);
            $ownedAlbum->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedAlbum(Album $ownedAlbum): static
    {
        if ($this->owned_albums->removeElement($ownedAlbum)) {
            // set the owning side to null (unless already changed)
            if ($ownedAlbum->getOwner() === $this) {
                $ownedAlbum->setOwner(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Album>
     */
    public function getSharedAlbums(): Collection
    {
        return $this->shared_albums;
    }

    public function addSharedAlbum(Album $sharedAlbum): static
    {
        if (!$this->shared_albums->contains($sharedAlbum)) {
            $this->shared_albums->add($sharedAlbum);
        }

        return $this;
    }

    public function removeSharedAlbum(Album $sharedAlbum): static
    {
        $this->shared_albums->removeElement($sharedAlbum);

        return $this;
    }

    public function getUsername(): string
    {
        return explode('@', $this->email)[0];
    }

    public function newAlbum(): Album
    {
        $album = new Album();
        $this->addOwnedAlbum($album);
        $album->setName('Default');
        return $album;
    }
}
