<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;

/**
 * Class CommentFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CommentFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(20, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setEmail($this->faker->unique()->email);
            $comment->setNickname($this->faker->unique()->userName);
            $comment->setContent($this->faker->paragraph);

            return $comment;
        });

        $this->manager->flush();
    }
}
