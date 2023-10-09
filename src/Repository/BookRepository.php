<?php

namespace App\Repository;

use App\Entity\Book;
use App\Service\DateManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry       $registry,
        protected DateManager $dateManager,
    )
    {
        parent::__construct($registry, Book::class);
    }

    public function searchBooks(string $text): array
    {
        $wrappedLowercaseText = strtolower('%' . $text . '%');
        $queryBuilder = $this->createQueryBuilder('r');
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
}