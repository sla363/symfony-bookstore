<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Currency;
use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Money\Money;

class AppFakeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $czkCurrency = new Currency();
        $czkCurrency->setCode('CZK');
        $manager->persist($czkCurrency);

        $eurCurrency = new Currency();
        $eurCurrency->setCode('EUR');
        $manager->persist($eurCurrency);

        $genres = [];
        for ($i = 0; $i < 10; $i++) {
            $genre = new Genre();
            $genre->setName($faker->word());
            $genres[] = $genre;
            $manager->persist($genre);
        }

        $authors = [];
        for ($i = 0; $i < 20; $i++) {
            $author = new Author();
            $author->setFirstName($faker->firstName());
            $author->setLastName($faker->lastName());
            $authors[] = $author;
            $manager->persist($author);
        }

        for ($i = 0; $i < 100; $i++) {
            $book = new Book();
            $book->setAuthor($faker->randomElement($authors));
            $book->setGenre($faker->randomElement($genres));
            $book->setTitle($faker->sentence(3));
            $book->setDescription($faker->paragraph(5));
            $book->setPrice(new Money($faker->numberBetween(90, 1500) * 100, new \Money\Currency($czkCurrency->getCode())));
            $book->setIsbn($faker->isbn10());
            $book->setPublishedDate(new \DateTimeImmutable($faker->date()));
            $book->setCurrency($czkCurrency);
            $manager->persist($book);
        }

        $manager->flush();
    }

}