<?php

namespace App\Movie\Transformer;

use App\Entity\Genre;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbGenreTransformer implements  DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf("Value must be a string, %s given.", gettype($value)));
        }

        return (new Genre())->setName($value);
    }

    public function reverseTransform(mixed $value): mixed
    {
        throw new \RuntimeException('Not implemented.');
    }
}
