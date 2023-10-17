<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Author;
use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    private ?Author $author;

    protected function setUp(): void
    {
        $this->author = new Author();
    }

    protected function tearDown(): void
    {
        $this->author = null;
    }


    public function testGetFullName(): void
    {
        $author = $this->author;
        $this->assertNotNull($author);
        $author->setFirstName('John');
        $author->setLastName('Doe');
        $this->assertEquals('John Doe', $author->getFullName());
    }

    public function testAddBook(): void
    {
        $author = $this->author;
        $this->assertNotNull($author);
        $book = new Book();
        $book
            ->setIsbn('123123123')
            ->setTitle('Test book')
            ->setDescription('This is a test book.');

        $secondBook = new Book();
        $secondBook
            ->setIsbn('9999999')
            ->setTitle('The greatest book of the books!')
            ->setDescription('Another great test book.');

        $author->addBook($book);
        $author->addBook($secondBook);

        $retrievedFirstBook = $author->getBooks()->filter(fn(Book $book) => $book->getIsbn() === '123123123')->first();
        $this->assertEquals($book, $retrievedFirstBook);

        $retrievedSecondBook = $author->getBooks()->filter(fn(Book $book) => $book->getIsbn() === '9999999')->first();
        $this->assertEquals($secondBook, $retrievedSecondBook);
    }
}