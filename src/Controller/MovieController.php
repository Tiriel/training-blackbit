<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Event\MovieCreatedEvent;
use App\Event\MovieEditedEvent;
use App\Event\MovieUnderageEvent;
use App\Form\MovieType;
use App\Movie\Enum\SearchTypeEnum;
use App\Movie\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Security\Voter\MovieVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/movie')]
class MovieController extends AbstractController
{
    public function __construct(
        private readonly MovieRepository $repository,
        private readonly EventDispatcherInterface $dispatcher,
    ) {}

    #[Route('', name: 'app_movie_index')]
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'movies' => $this->repository->findAll(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_movie_show')]
    public function show(Movie $movie, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted(MovieVoter::VIEW, $movie);
        $manager->getUnitOfWork()->markReadOnly($movie);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/new', name: 'app_movie_new')]
    #[Route('/{id<\d+>}/edit', name: 'app_movie_edit')]
    public function save(Request $request, ?Movie $movie = null): Response
    {
        $this->denyAccessUnlessGranted(MovieVoter::EDIT, $movie);
        $movie ??= new Movie();
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$movie->getId() && ($user = $this->getUser()) instanceof User) {
                $movie->setCreatedBy($user);
            }
            $this->repository->save($movie, true);

            $event = 'app_movie_new' === $request->attributes->get('_route')
                ? MovieCreatedEvent::class
                : MovieEditedEvent::class;
            $this->dispatcher->dispatch(new $event($movie));

            return $this->redirectToRoute('app_movie_show', ['id' => $movie->getId()]);
        }

        return $this->render('movie/save.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/omdb/{title}', name: 'app_movie_omdb')]
    public function omdb(string $title, MovieProvider $provider): Response
    {
        $movie = $provider->getMovieByTitle($title);
        $this->denyAccessUnlessGranted(MovieVoter::VIEW, $movie);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    public function decades(): Response
    {
        $decades = [1970, 1980, 2000];

        return $this->render('includes/_decades.html.twig', [
            'decades' => $decades
        ])->setMaxAge(3600);
    }

    protected function denyAccessUnlessGranted(mixed $attribute, mixed $subject = null, string $message = 'Access denied.'): void
    {
        try {
            parent::denyAccessUnlessGranted($attribute, $subject, $message);
        } catch (AccessDeniedException $e) {
            $user = $this->getUser();
            if (\in_array($attribute, [MovieVoter::VIEW, MovieVoter::EDIT]) && $user instanceof User) {
                $this->dispatcher->dispatch(new MovieUnderageEvent($subject ?? new Movie(), $user));
            }

            throw $e;
        }
    }
}
