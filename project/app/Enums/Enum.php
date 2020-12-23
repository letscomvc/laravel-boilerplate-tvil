<?php

namespace App\Enums;

use App\Enums\Exceptions\InvalidEnumValueException;

abstract class Enum
{
    private static $constCacheArray = null;

    public static function getConstants()
    {
        if (self::$constCacheArray == null) {
            self::$constCacheArray = [];
        }

        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    public static function getConstantsKeys()
    {
        return array_keys(self::getConstants());
    }

    public static function getConstantsValues()
    {
        return array_values(self::getConstants());
    }

    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true)
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }

    public static function validateValue($value, $strict = true)
    {
        if (!self::isValidValue($value, $strict)) {
            throw new InvalidEnumValueException("Invalid enum value [$value].");
        }

        return true;
    }

    public static function getStringValues(): string
    {
        return implode(',', self::getConstantsValues());
    }
}
