<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Table(name: '`author`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('name')]
class Author implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Name cannot be blank')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Name must be at least {{ limit }} characters long',
        maxMessage: 'Name cannot be longer than {{ limit }} characters'
    )]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    /**
     * @var Collection<int, Book>
     *     mappedBy anh xa (quan he n-n voi Book anh xa voi truong authors)
     */
    #[ManyToMany(targetEntity: Book::class, mappedBy: 'authors', cascade: ['persist', 'remove'])]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Book $book
     * @return $this
     */
    public function addBooks(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addAuthor($this);
        }

        return $this;
    }

    public function removeBooks(Book $book): void
    {
        $this->books->removeElement($book);
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->created_at = new DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(): void
    {
        $this->deleted_at = new DateTimeImmutable();
    }

    public function jsonSerialize(): array
    {
        $bookData = [];

        /** @var Book $book */
        foreach ($this->books as $book) {
            $bookData[] = [
                'id' => $book->getId(),
                'name' => $book->getName(),
                'price' => $book->getPrice(),
                'quantity' => $book->getQuantity(),
            ];
        }

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'created_at' => $this->getCreatedAt() ? $this->getCreatedAt()->format('d/m/Y') : '',
            'books' => $bookData
        ];
    }

    public function __toString()
    {
        return $this->name;
    }
}
