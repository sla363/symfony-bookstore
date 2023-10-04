<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'app_currency')]
class Currency
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $code;

    #[ORM\OneToMany(mappedBy: 'currency', targetEntity: Book::class)]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }


    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function setBooks(Collection $books): static
    {
        $this->books = $books;
        return $this;
    }

    public function addBook(Book $book): static {
        try {
            $books = $this->getBooks();
        } catch (\Error $e) {
            $books = new ArrayCollection();
        }
        if ($books->isEmpty() || !$books->contains($book)) {
            $books->add($book);
            $this->setBooks($books);
            $book->setCurrency($this);
        }

        return $this;
    }
}