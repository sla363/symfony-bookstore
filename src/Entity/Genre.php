<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_genre')]
class Genre
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * @var Collection<int, Book> $books
     */
    #[ORM\OneToMany(mappedBy: 'genre', targetEntity: Book::class)]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Collection<int, Book> $books
     */
    public function setBooks(Collection $books): static
    {
        $this->books = $books;
        return $this;
    }

    public function addBook(Book $book): static
    {
        try {
            $books = $this->getBooks();
        } catch (\Error $e) {
            $books = new ArrayCollection();
        }
        if ($books->isEmpty() || !$books->contains($book)) {
            $books->add($book);
            $this->setBooks($books);
            $book->setGenre($this);
        }

        return $this;
    }
}
