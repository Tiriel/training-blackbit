<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(Request $request, UrlGeneratorInterface $generator): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController::index',
        ]);
    }

    #[Route('/{!id<\d+>?0}/{action<\w+>?show}', name: 'app_book_show', methods: ['GET', 'POST'])]
    public function show(int $id, string $action): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController::show - Action : '.$action. ' - Id: '.$id,
        ]);
    }
}
