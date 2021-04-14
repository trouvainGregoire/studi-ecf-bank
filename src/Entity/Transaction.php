<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups(
     *  {"banker:list"}
     * )
     */
    private $createdAt;

    public function __construct()
    {
        $this->setType('credit');
        $this->setAmount(0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }
}
