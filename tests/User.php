<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Illuminate\Database\Eloquent\Model;

/**
 * The testing user model.
 *
 * @property ?CasesCollection<int, BitwiseEnum> $bitwise
 * @property ?CasesCollection<int, BackedEnum> $numbers
 * @property ?CasesCollection<int, PureEnum> $pureNumbers
 */
final class User extends Model
{
    /**
     * Indicates if all mass assignment is enabled.
     *
     * @var bool
     */
    protected static $unguarded = true;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bitwise' => CasesCollection::class . ':' . BitwiseEnum::class,
        'numbers' => CasesCollection::class . ':' . BackedEnum::class,
        'pureNumbers' => CasesCollection::class . ':' . PureEnum::class,
        'invalid' => CasesCollection::class . ':InvalidEnum',
    ];
}
