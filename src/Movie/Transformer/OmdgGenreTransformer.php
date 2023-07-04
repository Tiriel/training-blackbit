<?php

namespace App\Movie\Transformer;

use App\Entity\Genre;
use Symfony\Component\Form\DataTransformerInterface;

class OmdgGenreTransformer implements  DataTransformerInterface
{
    public function transform(mixed $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf("Value must be a string, %s given.", gettype($value)));
        }

        return (new Genre())->setName($value);
    }

    public function reverseTransform(mixed $value)
    {
        throw new \RuntimeException('Not implemented.');
    }
}
