<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ValidateUsernameService
{
    private UserRepository $userRepository;
    private SwearWordChecker $swearWordChecker;
    private int $minLength;
    private int $maxLength;

    public function __construct(UserRepository $userRepository, SwearWordChecker $swearWordChecker, ParameterBagInterface $params)
    {
        $this->userRepository = $userRepository;
        $this->swearWordChecker = $swearWordChecker;
        $this->minLength = (int) $params->get('app.min_username_length', 1);
        $this->maxLength = (int) $params->get('app.max_username_length');
    }

    //excludeUserId is to prevent the user's new username from being rejected if it matches their current username
    public function validate(?string $username, ?string $excludeUserId = null): ?string
    {
        if (!is_string($username)) {
            return 'Username must be a string';
        }

        if (preg_match('/\s/', $username)) {
            return 'Username must not contain spaces';
        }

        $len = strlen($username);

        if ($len < $this->minLength || $len > $this->maxLength) {
            return 'Username must be between ' . $this->minLength . ' and ' . $this->maxLength . ' characters';
        }

        if ($this->swearWordChecker->containsSwearWord($username)) {
            return 'Username contains inappropriate language';
        }

        $existing = $this->userRepository->findOneBy(['username' => $username]);

        if ($existing && (!$excludeUserId || $existing->getId() !== $excludeUserId)) {
            return 'Username already in use';
        }

        return null;
    }
}
