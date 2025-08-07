<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasLabel
{
    public function label()
    {
        $enumClass = class_basename(static::class);

        $key = sprintf(
            'enums.%s.%s',
            Str::snake($enumClass),
            strtolower($this->name)
        );

        $label = __($key);

        // Fallback: auto-generate label if translation not found
        return $label === $key
            ? Str::of($this->name)->replace('_', ' ')->lower()
            : Str::lower($label);
    }
}
