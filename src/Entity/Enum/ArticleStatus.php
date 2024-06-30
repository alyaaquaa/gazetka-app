<?php
/**
 * Article Status.
 */

namespace App\Entity\Enum;

/**
 * Class ArticleStatus.
 */
class ArticleStatus
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';

    /**
     * Mapuje wartość liczbową na stałą ArticleStatus.
     *
     * @param int $value the numeric value to be mapped
     *
     * @return string ArticleStatus
     *
     * @throws \InvalidArgumentException if the provided value is not valid
     */
    public static function from(int $value): string
    {
        return match ($value) {
            1 => self::DRAFT,
            2 => self::PUBLISHED,
            default => throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ArticleStatus', $value)),
        };
    }
}
