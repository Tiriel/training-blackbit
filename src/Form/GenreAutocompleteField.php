<?php

namespace App\Form;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class GenreAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Genre::class,
            'placeholder' => 'Choose a Genre',
            'choice_label' => 'name',
            'multiple' => true,

            'query_builder' => function (GenreRepository $genreRepository) {
                return $genreRepository->createQueryBuilder('genre');
            },
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
