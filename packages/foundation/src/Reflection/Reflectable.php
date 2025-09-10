<?php

namespace Lasagna\Foundation\Reflection;

use Lasagna\Foundation\Value\ValueProperty;

interface Reflectable
{
    /**
     * Get information about the class' properties.
     *
     * @return ValueProperty[]
     */
    public static function reflect(): array;
}
