<?php

namespace Rexlabs\Enum\Traits;

use Rexlabs\Enum\Enum;
use Rexlabs\Enum\Exceptions\InvalidKeyException;

trait HasEnum
{
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->hasEnumDecodeKey($key)) {
            $value = $this->decodeEnum($key, $value);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if ($value !== null && $this->hasEnumDecodeKey($key)) {
            if ($this->hasCast($key)) {
                $value = $this->castAttribute($key, $value);
            }

            $encoding = $this->getEnumEncoding($key);
            
            if ($value instanceof $encoding['class']) {
                $value = $value->key();
            } elseif (gettype($encoding['class']::keys()[0]) != gettype($value)) {
                $value = $encoding['class']::keyForValue($value);
            }

            if ($encoding['column'] != $key) {
                $this->setAttribute($encoding['column'], $value);
            } else {
                $this->attributes[$key] = $value;
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Determine whether an attribute should be decode to an enum value.
     *
     * @param  string  $key
     * @return bool
     */
    protected function hasEnumDecodeKey($key): bool
    {
        return array_key_exists($key, $this->getEnumEncodings());
    }

    protected function decodeEnum(string $key, $value)
    {
        $encoding = $this->getEnumEncoding($key);

        $enumClass = $encoding['class'];
        $value = $encoding['column'] == $key ? $value : $this->getAttribute($encoding['column']);

        try {
            return $enumClass::valueForKey($value);
        } catch (InvalidKeyException $ex) {
            return $value;
        }
    }

    protected function getEnumEncodings()
    {
        return property_exists($this, 'decodeEnums') ? $this->decodeEnums : [];
    }

    protected function getEnumEncoding(string $key)
    {
        $encoding = $this->getEnumEncodings()[$key];

        $class = is_string($encoding)
            ? $encoding : $encoding[1];
        $column = is_string($encoding)
            ? $key : $encoding[0];

        if (! is_subclass_of($class, Enum::class)) {
            throw new \ErrorException("{$class} is not Enum");
        }

        return compact('class', 'column');
    }
}
