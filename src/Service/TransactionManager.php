<?php

namespace App\Service;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class TransactionManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
    }

    public function generateTransactionIdentifier(): string
    {
        do {
            $generatedTransactionIdentifier = 'TR' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            $existingTransaction = $this->entityManager->getRepository(Transaction::class)->findOneBy(['identifier' => $generatedTransactionIdentifier]);
        } while ($existingTransaction);
        return $generatedTransactionIdentifier;
    }

}