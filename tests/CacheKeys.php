<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\LaravelEnum\Concerns\EnumeratesCacheKeys;
use Cerbero\LaravelEnum\Capsules\CacheKey;

/**
 * The enum of cache keys.
 *
 * @method static CacheKey PinnedPosts(int $userId)
 */
enum CacheKeys: string
{
    use EnumeratesCacheKeys;

    case PinnedPosts = 'teams.{int $teamId}.users.{string $userId}.pinned_posts';
}
