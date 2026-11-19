<?php

namespace App\Support;

final class JsonValue
{
    public static function orderedKeys(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }
        if (! array_is_list($value)) {
            ksort($value, SORT_STRING);
        }

        return array_map(self::orderedKeys(...), $value);
    }
}
