<?php

namespace App\Twig\Components;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent()]
final class BookFormComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?Book $book;

    public function __construct(
        private readonly BookRepository $repository
    ) {}

    public function mount(?Book $book = null)
    {
        $this->book = $book ?? new Book();
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(BookType::class, $this->book);
    }

    public function hasErrors(): bool
    {
        return $this->getFormInstance()->isSubmitted() && !$this->getFormInstance()->isValid();
    }

    #[LiveAction]
    public function saveBook()
    {
        $this->submitForm();
        $this->repository->save($this->book, true);
    }
}
