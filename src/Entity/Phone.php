<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhoneRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $model;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Groups({"show_phone"})
     */
    private $catchPhrase;

    /**
     * @ORM\Column(type="text")
     * 
     * @Groups({"show_phone"})
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"show_phone"})
     */
    private $batteryPower;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"show_phone"})
     */
    private $osName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"show_phone"})
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups({"show_phone"})
     */
    private $memory;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $availability;

    /**
     * @ORM\ManyToOne(targetEntity=PhoneBrand::class, inversedBy="phones")
     * 
     * @Groups({"list_phones", "show_phone"})
     */
    private $brand;

    /**
     * @ORM\ManyToMany(targetEntity=Photo::class, mappedBy="phones")
     * 
     * @Groups({"show_phone"})
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
