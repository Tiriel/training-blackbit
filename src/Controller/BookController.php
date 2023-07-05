<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Security\Voter\BookVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book')]
class BookController extends AbstractController
{
    public function __construct(
        private readonly BookRepository $repository
    ) {}

    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $this->repository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{!id<\d+>}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id<\d+>}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function save(Request $request, ?Book $book = null): Response
    {
        $book ??= new Book();
        if ($book->getId()) {
            $this->denyAccessUnlessGranted(BookVoter::EDIT, $book);
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (($user = $this->getUser()) instanceof User) {
                $book->setCreatedBy($user);
            }
            $this->repository->save($book, true);

            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        return $this->render('book/new.html.twig', [
            'form' => $form,
        ]);
    }
}
