<?php

namespace App\Security\Voter;

use App\Entity\Book;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

//#[AutoconfigureTag(name: 'security.voter', attributes: ['priority' => 300])]
class BookVoter extends Voter
{
    public const VIEW = 'book.view';
    public const EDIT = 'book.edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Book
            && \in_array($attribute, [self::VIEW, self::EDIT]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Book $subject */
        return match ($attribute) {
            self::VIEW => true,
            self::EDIT => $subject->getCreatedBy() === $token->getUser(),
            default => false,
        };
    }
}
