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

    public function searchBooks(string $text): array
    {
        $wrappedLowercaseText = strtolower('%' . $text . '%');
        $queryBuilder = $this->entityManager->getRepository(Book::class)->createQueryBuilder('r');
        $queryBuilder
            ->leftJoin('r.author', 'at')
            ->leftJoin('r.genre', 'g')
            ->where($queryBuilder->expr()->like($queryBuilder->expr()->lower('r.title'), ':text'))
            ->orWhere($queryBuilder->expr()->like($queryBuilder->expr()->lower('r.description'), ':text'))
            ->orWhere($queryBuilder->expr()->like($queryBuilder->expr()->lower('r.isbn'), ':text'))
            ->orWhere($queryBuilder->expr()->like($queryBuilder->expr()->lower('g.name'), ':text'))
            ->orWhere($queryBuilder->expr()->like($queryBuilder->expr()->lower('at.firstName'), ':text'))
            ->orWhere($queryBuilder->expr()->like($queryBuilder->expr()->lower('at.lastName'), ':text'))
            ->orWhere($queryBuilder->expr()->like(
                $queryBuilder->expr()->concat(
                    $queryBuilder->expr()->lower('at.firstName'), $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), $queryBuilder->expr()->lower('at.lastName'))
                ), ':text'
            ))
            ->orWhere($queryBuilder->expr()->like(
                $queryBuilder->expr()->concat(
                    $queryBuilder->expr()->lower('at.lastName'), $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), $queryBuilder->expr()->lower('at.firstName'))
                ), ':text'
            ))
            ->setParameter('text', $wrappedLowercaseText);

        $date = $this->dateManager->createDateFromMultipleFormats($text);
        if ($date) {
            $queryBuilder
                ->orWhere($queryBuilder->expr()->eq('r.publishedDate', ':date'))
                ->setParameter('date', $date);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getAllBooks(): array
    {
        return $this->entityManager->getRepository(Book::class)->findAll();
    }
}