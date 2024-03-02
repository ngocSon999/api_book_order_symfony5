<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\HasLifecycleCallbacks]
class Order implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $customerName = null;

    #[ORM\Column]
    private ?int $customerPhone = null;

    #[ORM\Column(length: 255)]
    private ?string $customerAddress = null;

    #[ORM\Column]
    private ?int $totalMoney = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: BookOrder::class, cascade: ['persist', 'remove'],orphanRemoval: true)]
    private Collection $bookOrders;

    public function __construct()
    {
        $this->bookOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookOrders(): Collection
    {
        return $this->bookOrders;
    }

    public function addBookOrder(BookOrder $bookOrder): void
    {
        if (!$this->bookOrders->contains($bookOrder)) {
            $this->bookOrders[] = $bookOrder;
            $bookOrder->setOrder($this);
        }
    }

    public function removeBookOrder(BookOrder $bookOrder): void
    {
        $this->bookOrders->removeElement($bookOrder);
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCustomerPhone(): ?int
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(int $customerPhone): static
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(string $customerAddress): static
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    public function getTotalMoney(): ?int
    {
        return $this->totalMoney;
    }

    public function setTotalMoney(int $totalMoney): static
    {
        $this->totalMoney = $totalMoney;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    #[ORM\PreDelete]
    public function setDeletedAt(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function jsonSerialize(): array
    {
        $bookOrders = [];
        /** @var BookOrder $bookOrder */
        foreach ($this->bookOrders as $bookOrder) {
            $bookOrders[] = [
                'book' => [
                    'name' => $bookOrder->getBook()->getName(),
                    'price' => $bookOrder->getBook()->getPrice()
                ],
                'quantity' => $bookOrder->getQuantity()
            ];
        }
        return [
            'id' => $this->id,
            'customerName' => $this->customerName,
            'customerAddress' => $this->customerAddress,
            'bookOrder' => $bookOrders
        ];
    }
}
