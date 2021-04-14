<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as BankAssert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @Vich\Uploadable
 * @ApiResource(
 *   collectionOperations={
 *     "get"={
 *      "access_control"="is_granted('ROLE_BANKER')",
 *      "normalization_context"={"groups"="banker:list"}
 *      }
 *     },
 *   itemOperations={"get"={"access_control"="is_granted('ROLE_BANKER')","normalization_context"={"groups"="banker:show"}}},
 * )
 */
class Client implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(max=180)
     * @Assert\Email
     * @BankAssert\IsUniqueEmail
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $idCardName;

    /**
     * @Vich\UploadableField(mapping="uploads", fileNameProperty="idCardName")
     *
     * @Assert\File(maxSize="5000k", mimeTypes={"application/pdf"}, mimeTypesMessage="Seul le format PDF est autorisé")
     *
     * @var File|null
     */
    private $idCarFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=Account::class, mappedBy="client", cascade={"persist", "remove"})
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity=Banker::class, inversedBy="clients")
     */
    private $banker;

    /**
     * @ORM\OneToMany(targetEntity=Recipient::class, mappedBy="client", cascade={"remove"})
     */
    private $recipients;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $city;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CLIENT';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthdate(): ?DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getIdCardName(): ?string
    {
        return $this->idCardName;
    }

    public function setIdCardName(?string $idCardName): void
    {
        $this->idCardName = $idCardName;
    }

    /**
     * @return File|null
     */
    public function getIdCarFile(): ?File
    {
        return $this->idCarFile;
    }

    /**
     * @param File|UploadedFile|null $idCarFile
     */
    public function setIdCarFile(?File $idCarFile = null): void
    {
        $this->idCarFile = $idCarFile;

        if (null !== $idCarFile) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        // set the owning side of the relation if necessary
        if ($account->getClient() !== $this) {
            $account->setClient($this);
        }

        $this->account = $account;

        return $this;
    }

    public function getBanker(): ?Banker
    {
        return $this->banker;
    }

    public function setBanker(?Banker $banker): self
    {
        $this->banker = $banker;

        return $this;
    }

    /**
     * @return Collection|Recipient[]
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(Recipient $recipient): self
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients[] = $recipient;
            $recipient->setClient($this);
        }

        return $this;
    }

    public function removeRecipient(Recipient $recipient): self
    {
        if ($this->recipients->removeElement($recipient)) {
            // set the owning side to null (unless already changed)
            if ($recipient->getClient() === $this) {
                $recipient->setClient(null);
            }
        }

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
