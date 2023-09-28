<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $publishedDate;

    #[ORM\Column(length: 255)]
    private string $isbn;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $price;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private Author $author;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private Genre $genre;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublishedDate(): \DateTimeImmutable
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(\DateTimeImmutable $publishedDate): static
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getGenre(): Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }
}
