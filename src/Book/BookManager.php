<?php

namespace App\Book;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BookManager
{
    public function __construct(
        private readonly BookRepository $repository,
        #[Autowire(service: 'mailer.mailer')]
        private readonly MailerInterface $mailer,
        #[Autowire('%app.books_per_age%')]
        private readonly int $booksPerPage,
        private readonly Security $security
    ) {}

    public function getByTitle(string $title): ?Book
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $this->mailer->send(new Message());
        }

        return $this->repository->findOneBy(['title' => $title]);
    }

    public function associateUser(Book $book): Book
    {
        if (($user = $this->security->getUser()) instanceof User) {
            $book->setCreatedBy($user);
            $this->repository->save($book, true);
        }

        return $book;
    }

    public function getPaginated(): iterable
    {
        return $this->repository->findBy([], [], $this->booksPerPage);
    }
}
