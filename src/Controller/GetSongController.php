<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/song/{id<\d+>?1}', name: 'app_song_get')]
class GetSongController extends AbstractController
{
    public function __invoke()
    {
        return $this->render('song/index.html.twig', [
            'controller_name' => GetSongController::class
        ]);
    }
}
