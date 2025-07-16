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
    public function validate(?string $username, ?string $excludeUserId = null): ?array
    {

        if (!is_string($username)) {
            return ['code' => 'ERR_USERNAME_INVALID_TYPE', 'message' => 'Username must be a string'];
        }

        if (preg_match('/\s/', $username)) {
            return ['code' => 'ERR_USERNAME_CONTAINS_SPACES', 'message' => 'Username must not contain spaces'];
        }

        $len = strlen($username);

        if ($len < $this->minLength || $len > $this->maxLength) {
            return ['code' => 'ERR_USERNAME_LENGTH', 'message' => 'Username must be between ' . $this->minLength . ' and ' . $this->maxLength . ' characters'];
        }

        if ($this->swearWordChecker->containsSwearWord($username)) {
            return ['code' => 'ERR_USERNAME_INAPPROPRIATE', 'message' => 'Username contains inappropriate language'];
        }

        $existing = $this->userRepository->findOneBy(['username' => $username]);

        if ($existing && (!$excludeUserId || $existing->getId()->toString() !== $excludeUserId)) {
            return ['code' => 'ERR_USERNAME_TAKEN', 'message' => 'Username already in use'];
        }

        return null;
    }
}
