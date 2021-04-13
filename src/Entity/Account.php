<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Serializable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @Vich\Uploadable
 */
class Account implements Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $balance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $identifier;

    /**
     * @ORM\OneToOne(targetEntity=Client::class, inversedBy="account", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="account", orphanRemoval=true)
     */
    private $transactions;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $idDeleteSignatureName;

    /**
     * @Vich\UploadableField(mapping="uploads", fileNameProperty="idDeleteSignatureName")
     *
     * @Assert\File(maxSize="5000k", mimeTypes={"application/pdf"}, mimeTypesMessage="Seul le format PDF est autorisé")
     *
     * @var File|null
     */
    private $idDeleteSignatureFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTimeInterface|null
     */
    private $updatedAt;

    public function __construct()
    {
        $this->setBalance(0);
        $this->setStatus('pending');
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdDeleteSignatureName(): ?string
    {
        return $this->idDeleteSignatureName;
    }

    /**
     * @param mixed $idDeleteSignatureName
     */
    public function setIdDeleteSignatureName(?string $idDeleteSignatureName): void
    {
        $this->idDeleteSignatureName = $idDeleteSignatureName;
    }

    /**
     * @return File|null
     */
    public function getIdDeleteSignatureFile(): ?File
    {
        return $this->idDeleteSignatureFile;
    }

    /**
     * @param File|UploadedFile|null $idDeleteSignatureFile
     */
    public function setIdDeleteSignatureFile(?File $idDeleteSignatureFile = null): void
    {
        $this->idDeleteSignatureFile = $idDeleteSignatureFile;

        if (null !== $idDeleteSignatureFile) {
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

    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->client,
                $this->balance,
                $this->identifier,
                $this->status,
                $this->transactions,
                $this->idDeleteSignatureName,
                $this->updatedAt
            )
        );
    }

    public function unserialize($data)
    {
        list(
            $this->id,
            $this->client,
            $this->balance,
            $this->identifier,
            $this->status,
            $this->transactions,
            $this->idDeleteSignatureName,
            $this->updatedAt
            ) = unserialize($data);
    }
}
