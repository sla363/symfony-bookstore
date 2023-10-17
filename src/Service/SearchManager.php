<?php

namespace App\Service;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class SearchManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected DateManager            $dateManager,
    )
    {
    }

    /**
     * @return array<int, Book>
     */
    public function searchBooks(string $text): array
    {
        return $this->entityManager->getRepository(Book::class)->searchBooks($text);
    }

    /**
     * @return array<int, Book>
     */
    public function getAllBooks(): array
    {
        return $this->entityManager->getRepository(Book::class)->findAll();
    }
}