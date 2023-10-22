<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Book;
use App\Entity\Genre;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    public function testGenreAddBook(): void
    {
        $genre = (new Genre())
            ->setName('Drama');
        $book = (new Book())
            ->setTitle('Once upon a time in the west...');
        $genre->addBook($book);
        $this->assertEquals($book, $genre->getBooks()->first());

        $secondBook = (new Book())
            ->setTitle('Alice in Wonderland');
        $genre->addBook($secondBook);
        $books = $genre->getBooks();
        $this->assertEquals($secondBook, $books->next());
    }
}