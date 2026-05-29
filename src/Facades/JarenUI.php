<?php

namespace JarenUI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string  version()
 * @method static static  theme(string $theme)
 * @method static string|null getTheme()
 * @method static static  accent(string $color)
 * @method static string|null getAccentColor()
 * @method static string  renderStyles()
 *
 * @see \JarenUI\JarenUI
 */
class JarenUI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'jarenui';
    }
}
