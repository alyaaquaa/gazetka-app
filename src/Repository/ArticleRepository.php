<?php
/**
 * Article repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ArticleRepository.
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial article.{id, createdAt, updatedAt, title, content}',
                'partial category.{id, title}'
            )
            ->join('article.category', 'category')
            ->orderBy('article.updatedAt', 'DESC');
    }

    /**
     * Count articles by category.
     *
     * @param Category $category Category
     *
     * @return int Number of articles in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('article.id'))
            ->where('article.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Article $article Article entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Article $article): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($article);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Article $article Article entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Article $article): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($article);
        $this->_em->flush();
    }

    /**
     * Query articles by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(\App\Entity\User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('article.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }

    /**
     * Find all articles.
     *
     * @return Article[] Returns an array of Article objects
     */
    public function findAllArticles()
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('article');
    }
}
