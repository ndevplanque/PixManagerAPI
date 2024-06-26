<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AlbumRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[Groups(['users', 'albums','shared'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['users', 'albums', 'shared'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['users','albums'])]
    #[ORM\Column]
    private DateTimeImmutable $created_at;

    #[Groups(['shared','albums'])]
    #[ORM\ManyToMany(targetEntity: AppUser::class, mappedBy: 'shared_albums')]
    private Collection $shared_to;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'album', cascade: ['persist'], orphanRemoval: true)]
    private Collection $photos;


    #[ORM\ManyToOne(targetEntity: AppUser::class, cascade: ["persist"], inversedBy: 'albums')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['albums'])]
    private ?AppUser $owner = null;

    /** @deprecated use AppUser::newAlbum() */
    public function __construct()
    {
        $this->created_at = new DateTimeImmutable();
        $this->shared_to = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }
    public function setCreatedAtValue(): void
    {
        $this->created_at = new DateTimeImmutable();
    }

    /**
     * @return Collection<int, AppUser>
     */
    public function getSharedTo(): Collection
    {
        return $this->shared_to;
    }

    public function addSharedTo(AppUser $sharedTo): static
    {
        if (!$this->shared_to->contains($sharedTo)) {
            $this->shared_to->add($sharedTo);
            $sharedTo->addSharedAlbum($this);
        }

        return $this;
    }

    public function removeSharedTo(AppUser $sharedTo): static
    {
        if ($this->shared_to->removeElement($sharedTo)) {
            $sharedTo->removeSharedAlbum($this);

        }

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setAlbum($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getAlbum() === $this) {
                $photo->setAlbum(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?AppUser
    {
        return $this->owner;
    }

    public function setOwner(?AppUser $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function equals(?Album $album): bool
    {
        return $album !== null && $this->getId() === $album->getId();
    }
}
