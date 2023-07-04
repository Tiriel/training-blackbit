<?php

namespace App\Book;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Message;

class BookManager
{
    public function __construct(
        private readonly BookRepository $repository,
        #[Autowire(service: 'mailer.mailer')]
        private readonly MailerInterface $mailer,
        #[Autowire('%app.books_per_age%')]
        private readonly int $booksPerPage,
    ) {}

    public function getByTitle(string $title): ?Book
    {
        $this->mailer->send(new Message());
        return $this->repository->findOneBy(['title' => $title]);
    }

    public function getPaginated(): iterable
    {
        return $this->repository->findBy([], [], $this->booksPerPage);
    }
}
