<?php

namespace Cerbero\LaravelEnum\Concerns;

use Cerbero\Enum\Concerns\SelfAware as BaseSelfAware;
use Cerbero\LaravelEnum\Enums;
use Illuminate\Support\Facades\Lang;
use ValueError;

/**
 * The trait to make an enum self-aware.
 */
trait SelfAware
{
    use BaseSelfAware {
        BaseSelfAware::resolveItem as baseResolveItem;
    }

    /**
     * Retrieve the given item of this case.
     *
     * @template TItemValue
     *
     * @param (callable(self): TItemValue)|string $item
     * @return TItemValue
     * @throws ValueError
     */
    public function resolveItem(callable|string $item): mixed
    {
        try {
            return $this->baseResolveItem($item);
        } catch (ValueError $e) {
            try {
                /** @var string $item */
                return $this->resolveTranslation($item);
            } catch (ValueError) {
                throw $e;
            }
        }
    }

    /**
     * Retrieve the translation of the given key for this case.
     *
     * @throws ValueError
     */
    public function resolveTranslation(string $key): string
    {
        $translationKey = Enums::resolveTranslationKey($this, $key);
        /** @var string */
        $translation = Lang::get($translationKey);

        if ($translation !== $translationKey) {
            return $translation;
        }

        throw new ValueError(sprintf('The case %s::%s has no "%s" translation set', self::class, $this->name, $key));
    }
}
