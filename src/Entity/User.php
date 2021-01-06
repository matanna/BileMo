<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups({"list_users", "show_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"list_users", "show_user"})
     * 
     * @Assert\NotBlank(message="Le 'username' ne doit pas ếtre vide.")
     * @Assert\Length(
     *                min=3,
     *                minMessage="Le 'username' doit comporter au moins 3 caractères"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"list_users", "show_user"})
     * 
     * @Assert\Email(
     *               message="'{{ value }}' Cet email n'est pas valide.")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     */
    private $client;

    /**
     * @ORM\Column(type="array", nullable=true)
     * 
     * @Groups({"show_user"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     * @Groups({"show_user"})
     */
    private $dateAtCreated;

    /**
     * @ORM\Column(type="string", nullable=true)
     * 
     * @Groups({"show_user"})
     * 
     * Assert\Length(
     *               min=8,
     *               message="Le numéro de téléphone doit comporter au moins 8 caratctères"
     * )
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"show_user"})
     */
    private $profilPicture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"show_user"})
     */
    private $adress;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getRoles(): ?array
    {
        return ['ROLE_USER'];
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt() {}

    public function eraseCredentials() {}

    public function getDateAtCreated(): ?\DateTimeInterface
    {
        return $this->dateAtCreated;
    }

    public function setDateAtCreated(?\DateTimeInterface $dateAtCreated): self
    {
        $this->dateAtCreated = $dateAtCreated;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?string $profilPicture): self
    {
        $this->profilPicture = $profilPicture;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

}