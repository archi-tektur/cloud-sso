<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Auth;

use App\Domain\Shared\Query\Exception\NotFoundException;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\User\Query\Mysql\MysqlUserReadModelRepository;
use Assert\AssertionFailedException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthProvider implements UserProviderInterface
{
    private MysqlUserReadModelRepository $userReadModelRepository;

    public function __construct(MysqlUserReadModelRepository $userReadModelRepository)
    {
        $this->userReadModelRepository = $userReadModelRepository;
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     * @throws AssertionFailedException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $email
     * @return Auth|UserInterface
     * @throws NotFoundException
     * @throws AssertionFailedException
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $email)
    {
        // @var array $user
        [$uuid, $email, $hashedPassword] = $this->userReadModelRepository->getCredentialsByEmail(
            Email::fromString($email)
        );

        return Auth::create($uuid, $email, $hashedPassword);
    }

    public function supportsClass(string $class): bool
    {
        return Auth::class === $class;
    }
}