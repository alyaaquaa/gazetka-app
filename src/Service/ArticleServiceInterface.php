<?php
/**
 * Article service interface.
 */

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ArticleServiceInterface.
 */
interface ArticleServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int       $page   Page number
     * @param User|null $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, ?User $author = null): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Article $article Article entity
     */
    public function save(Article $article): void;

    /**
     * Delete entity.
     *
     * @param Article $article Article entity
     */
    public function delete(Article $article): void;

    /**
     * Get Admin Paginated List entity.
     *
     * @param int $page Page number
     */
    public function getAdminPaginatedList(int $page): PaginationInterface;
}
