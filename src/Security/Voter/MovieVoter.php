<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

#[AutoconfigureTag(name: 'security.voter', attributes: ['priority' => 300])]
class MovieVoter extends Voter
{
    public const VIEW = 'movie.view';
    public const EDIT = 'movie.edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Movie
            && \in_array($attribute, [self::VIEW, self::EDIT]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Movie $subject */
        return match ($attribute) {
            self::VIEW => $this->checkView($subject, $user),
            self::EDIT => $this->checkEdit($subject, $user),
            default => false,
        };
    }

    public function checkView(Movie $movie, User $user): bool
    {
        if ('G' === $movie->getRated()) {
            return true;
        }

        $age = $user->getBirthday()?->diff(new \DateTimeImmutable())->y ?? null;

        return match ($movie->getRated()) {
            'PG', 'PG-13' => $age && $age>= 13,
            'R', 'NC-17' => $age && $age >= 17,
            default => false,
        };
    }

    public function checkEdit(Movie $movie, User $user): bool
    {
        return $this->checkView($movie, $user) && $user === $movie->getCreatedBy();
    }
}
