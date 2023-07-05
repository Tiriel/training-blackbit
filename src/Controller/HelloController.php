<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    #[Route('/hello/{name}', name: 'app_hello', requirements: ['name' => '([\pL]|[- ])+'], defaults: ['name' => 'World'])]
    public function index(string $name, #[Autowire('%app.sf_version%')] string $sfVersion): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLOWN');
        dump($sfVersion);

        if (!\in_array('ROLE_PROVIDER', $this->getUser()->getRoles())) {
            /// WRONG!!
        }

        return $this->render('hello/index.html.twig', [
            'controller_name' => $name,
        ]);
    }
}
