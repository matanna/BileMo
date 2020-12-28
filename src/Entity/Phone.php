<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $model;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $catchPhrase;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $batteryPower;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $osName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $memory;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $availability;

    /**
     * @ORM\ManyToOne(targetEntity=PhoneBrand::class, inversedBy="_phones")
     */
    private $brand;

    /**
     * @ORM\ManyToMany(targetEntity=Photo::class, mappedBy="phones")
     */
    private $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getCatchPhrase(): ?string
    {
        return $this->catchPhrase;
    }

    public function setCatchPhrase(?string $catchPhrase): self
    {
        $this->catchPhrase = $catchPhrase;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getBatteryPower(): ?string
    {
        return $this->batteryPower;
    }

    public function setBatteryPower(?string $batteryPower): self
    {
        $this->batteryPower = $batteryPower;

        return $this;
    }

    public function getOsName(): ?string
    {
        return $this->osName;
    }

    public function setOsName(string $osName): self
    {
        $this->osName = $osName;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getMemory(): ?int
    {
        return $this->memory;
    }

    public function setMemory(?int $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(?bool $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getBrand(): ?PhoneBrand
    {
        return $this->brand;
    }

    public function setBrand(?PhoneBrand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->addPhone($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            $photo->removePhone($this);
        }

        return $this;
    }
}
