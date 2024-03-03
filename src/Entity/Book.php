<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: '`book`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('name')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Name cannot be blank')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Name cannot be longer than {{ limit }} characters'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'quantity cannot be blank')]
    #[Assert\PositiveOrZero(message: 'Trường số lượng phải lớn hơn hoặc bằng 0')]
    private ?string $quantity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'price cannot be blank')]
    #[Assert\PositiveOrZero(message: 'Trường giá phải lớn hơn hoặc bằng 0')]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['book'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, Author>
     * Khi có khai báo thì tự động tạo bảng book_author khi run migrate
     * inversedBy (nghich dao) quan he nay la noi dat o Auth voi truong tuong ung cua no
     * (truong hop nay laf truong books tren Auth)
     */
    #[ManyToMany(targetEntity: Author::class, inversedBy: "books", cascade: ['persist'])]
    private Collection $authors;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookOrder::class, cascade: ['persist', 'remove'])]
    private Collection $bookOrders;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->bookOrders = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    /**
     * @param Author $author
     * @return $this
     */
    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
            $author->addBooks($this);
        }

        return $this;
    }

    public function removeAuthor(Author $author): void
    {
        $this->authors->removeElement($author);
    }

    /**
     * @return Collection<int, BookOrder>
     */
    public function bookOrders(): Collection
    {
        return $this->bookOrders;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

//    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

//    #[ORM\PrePersist]
//    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
    public function setDeletedAt(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function jsonSerialize(): array
    {
        $authors = [];

        /** @var Author $author */
        foreach ($this->getAuthors() as $author) {
            $authorData = [
                'id' => $author->getId(),
                'name' => $author->getName(),
            ];
            $authors[] = $authorData;
            unset($author);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'description' => $this->description,
            'created_ad' => $this->getCreatedAt() ? $this->getCreatedAt()->format('d/m/Y') : '',
            'authors' => $authors
        ];
    }

    public function __toString()
    {
        return $this->name;
    }
}
