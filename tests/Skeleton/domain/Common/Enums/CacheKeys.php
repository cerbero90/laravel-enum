<?php

declare(strict_types=1);

namespace Domain\Common\Enums;

use Cerbero\LaravelEnum\Concerns\EnumeratesCacheKeys;

/**
 * The enum to enumerate cache keys.
 *
 * @method static list<string> names()
 */
enum CacheKeys: string
{
    use EnumeratesCacheKeys;

    case PostComments = 'posts.{int $postId}.comments';
    case Tags = 'tags';
    case TeamMemberPosts = 'teams.{string $teamId}.users.{string $userId}.posts';
}