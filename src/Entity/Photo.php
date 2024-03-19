<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[Groups('photos')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('photos')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups('photos')]
    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[Groups('photos')]
    #[ORM\ManyToMany(targetEntity: Label::class, mappedBy: 'photos', cascade: ['persist'])]
    private Collection $labels;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AppUser $owner = null;

    /** @deprecated use AppUser::newPhoto() */
    public function __construct(string $name = null)
    {
        $this->created_at = new DateTimeImmutable();
        $this->labels = new ArrayCollection();
        $this->name = $name;
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

    /**
     * @return Collection<int, Label>
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    public function addLabel(Label $label): static
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
            $label->addPhoto($this);
        }

        return $this;
    }

    public function removeLabel(Label $label): static
    {
        if ($this->labels->removeElement($label)) {
            $label->removePhoto($this);
        }

        return $this;
    }

    /**
     * @param string[] $names
     */
    public function removeLabelsByName(array $names): static
    {
        /** @var Label[] $labels */
        $labels = $this->getLabels()->getValues();
        for ($i = 0; $i < count($names); $i++) {
            foreach ($labels as $label) {
                if ($label->getName() === $names[$i]) {
                    $this->removeLabel($label);
                    break;
                }
            }
        }
        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): static
    {
        $this->album = $album;

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
}
