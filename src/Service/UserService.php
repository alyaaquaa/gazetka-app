<?php
/**
 * User service.
 */

namespace App\Service;

use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    public $passwordHasher;
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param PaginatorInterface          $paginator      Paginator
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<User> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->findAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void
    {
        if (null === $user->getId()) {
            throw new \InvalidArgumentException('Cannot delete a user that does not exist.');
        }

        $this->userRepository->delete($user);
    }

    /**
     * Changes user's password.
     *
     * @param User   $user        The user entity
     * @param string $newPassword The new password to set
     */
    public function changePassword(User $user, string $newPassword): void
    {
        $encodedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($encodedPassword);
        $this->userRepository->save($user);
    }
}
