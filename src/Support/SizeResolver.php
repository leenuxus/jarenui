<?php

namespace JarenUI\Support;

/**
 * JarenUI SizeResolver
 *
 * Central size definition for all form components.
 * Supports xs | sm | md | lg | xl | 2xl.
 *
 * Usage inside a Blade component @php block:
 *
 *   $sz = \JarenUI\Support\SizeResolver::input($size);
 *   // $sz['height'], $sz['text'], $sz['icon'], $sz['px'], $sz['radius'], $sz['label']
 */
class SizeResolver
{
    /**
     * Input / Textarea / Select sizes.
     *
     * @return array{height:string, text:string, icon:string, px:string, radius:string, label:string, hint:string}
     */
    public static function input(string $size): array
    {
        return match ($size) {
            'xs'  => [
                'height' => 'h-7',
                'text'   => 'text-xs',
                'icon'   => 'w-3 h-3',
                'px'     => 'px-2',
                'radius' => 'rounded-[var(--radius)]',
                'label'  => 'text-[11px]',
                'hint'   => 'text-[10px]',
            ],
            'sm'  => [
                'height' => 'h-8',
                'text'   => 'text-[13px]',
                'icon'   => 'w-3.5 h-3.5',
                'px'     => 'px-2.5',
                'radius' => 'rounded-[var(--radius)]',
                'label'  => 'text-xs',
                'hint'   => 'text-[11px]',
            ],
            'lg'  => [
                'height' => 'h-11',
                'text'   => 'text-base',
                'icon'   => 'w-[18px] h-[18px]',
                'px'     => 'px-3.5',
                'radius' => 'rounded-[var(--radius-lg)]',
                'label'  => 'text-sm',
                'hint'   => 'text-xs',
            ],
            'xl'  => [
                'height' => 'h-[52px]',
                'text'   => 'text-lg',
                'icon'   => 'w-5 h-5',
                'px'     => 'px-4',
                'radius' => 'rounded-[var(--radius-lg)]',
                'label'  => 'text-base',
                'hint'   => 'text-sm',
            ],
            '2xl' => [
                'height' => 'h-16',
                'text'   => 'text-[22px]',
                'icon'   => 'w-6 h-6',
                'px'     => 'px-5',
                'radius' => 'rounded-[var(--radius-xl)]',
                'label'  => 'text-lg',
                'hint'   => 'text-base',
            ],
            default => [ // md
                'height' => 'h-[36px]',
                'text'   => 'text-[14px]',
                'icon'   => 'w-4 h-4',
                'px'     => 'px-3',
                'radius' => 'rounded-[var(--radius)]',
                'label'  => 'text-[13px]',
                'hint'   => 'text-[12px]',
            ],
        };
    }

    /**
     * Button sizes.
     *
     * @return array{height:string, text:string, px:string, icon:string, radius:string, gap:string}
     */
    public static function button(string $size): array
    {
        return match ($size) {
            'xs'  => ['height' => 'h-7',      'text' => 'text-xs',      'px' => 'px-2.5',  'icon' => 'w-3 h-3',      'radius' => 'rounded-[var(--radius)]',    'gap' => 'gap-1'],
            'sm'  => ['height' => 'h-8',      'text' => 'text-[13px]',  'px' => 'px-3',    'icon' => 'w-3.5 h-3.5',  'radius' => 'rounded-[var(--radius)]',    'gap' => 'gap-1.5'],
            'lg'  => ['height' => 'h-11',     'text' => 'text-base',    'px' => 'px-4.5',  'icon' => 'w-[18px] h-[18px]', 'radius' => 'rounded-[var(--radius-lg)]', 'gap' => 'gap-2'],
            'xl'  => ['height' => 'h-[52px]', 'text' => 'text-lg',      'px' => 'px-6',    'icon' => 'w-5 h-5',      'radius' => 'rounded-[var(--radius-lg)]', 'gap' => 'gap-2'],
            '2xl' => ['height' => 'h-16',     'text' => 'text-[22px]',  'px' => 'px-8',    'icon' => 'w-6 h-6',      'radius' => 'rounded-[var(--radius-xl)]', 'gap' => 'gap-2.5'],
            default => ['height' => 'h-9',    'text' => 'text-[14px]',  'px' => 'px-3.5',  'icon' => 'w-4 h-4',      'radius' => 'rounded-[var(--radius)]',    'gap' => 'gap-1.5'],
        };
    }

    /**
     * Checkbox sizes.
     *
     * @return array{box:string, check:string, text:string, desc:string, radius:string}
     */
    public static function checkbox(string $size): array
    {
        return match ($size) {
            'xs'  => ['box' => 'w-3.5 h-3.5', 'check' => 'w-2 h-1',      'text' => 'text-xs',     'desc' => 'text-[10px]', 'radius' => 'rounded'],
            'sm'  => ['box' => 'w-4 h-4',      'check' => 'w-2.5 h-1.5',  'text' => 'text-[13px]', 'desc' => 'text-[11px]', 'radius' => 'rounded'],
            'lg'  => ['box' => 'w-[22px] h-[22px]', 'check' => 'w-3 h-2', 'text' => 'text-base',   'desc' => 'text-sm',     'radius' => 'rounded-[5px]'],
            'xl'  => ['box' => 'w-[26px] h-[26px]', 'check' => 'w-3.5 h-2', 'text' => 'text-lg',  'desc' => 'text-base',   'radius' => 'rounded-[6px]'],
            '2xl' => ['box' => 'w-8 h-8',      'check' => 'w-4 h-2.5',    'text' => 'text-[22px]', 'desc' => 'text-lg',     'radius' => 'rounded-[8px]'],
            default => ['box' => 'w-[18px] h-[18px]', 'check' => 'w-2.5 h-1.5', 'text' => 'text-[14px]', 'desc' => 'text-xs', 'radius' => 'rounded'],
        };
    }

    /**
     * Switch / Toggle sizes.
     *
     * @return array{track:string, thumb:string, translate:string, text:string, desc:string}
     */
    public static function switch(string $size): array
    {
        return match ($size) {
            'xs'  => ['track' => 'w-7 h-4',         'thumb' => 'w-3 h-3',         'translate' => 'translate-x-3',     'text' => 'text-xs',     'desc' => 'text-[10px]'],
            'sm'  => ['track' => 'w-8 h-[18px]',    'thumb' => 'w-3.5 h-3.5',     'translate' => 'translate-x-[14px]','text' => 'text-[13px]', 'desc' => 'text-[11px]'],
            'lg'  => ['track' => 'w-12 h-7',         'thumb' => 'w-[22px] h-[22px]','translate' => 'translate-x-5',    'text' => 'text-base',   'desc' => 'text-sm'],
            'xl'  => ['track' => 'w-[58px] h-[34px]','thumb' => 'w-[28px] h-[28px]','translate' => 'translate-x-6',   'text' => 'text-lg',     'desc' => 'text-base'],
            '2xl' => ['track' => 'w-[72px] h-[42px]','thumb' => 'w-[34px] h-[34px]','translate' => 'translate-x-[30px]','text' => 'text-[22px]','desc' => 'text-lg'],
            default => ['track' => 'w-10 h-[22px]',  'thumb' => 'w-[18px] h-[18px]','translate' => 'translate-x-[18px]','text' => 'text-[14px]','desc' => 'text-xs'],
        };
    }

    /**
     * Slider sizes.
     *
     * @return array{track:string, thumb:string, label:string, value:string}
     */
    public static function slider(string $size): array
    {
        return match ($size) {
            'xs'  => ['track' => 'h-[3px]',  'thumb' => 'w-3.5 h-3.5',   'label' => 'text-xs',     'value' => 'text-xs'],
            'sm'  => ['track' => 'h-1',      'thumb' => 'w-4 h-4',        'label' => 'text-[13px]', 'value' => 'text-[13px]'],
            'lg'  => ['track' => 'h-1.5',    'thumb' => 'w-[22px] h-[22px]','label' => 'text-base', 'value' => 'text-base'],
            'xl'  => ['track' => 'h-2',      'thumb' => 'w-7 h-7',        'label' => 'text-lg',     'value' => 'text-lg'],
            '2xl' => ['track' => 'h-2.5',    'thumb' => 'w-8 h-8',        'label' => 'text-[22px]', 'value' => 'text-[22px]'],
            default => ['track' => 'h-[5px]','thumb' => 'w-[18px] h-[18px]','label' => 'text-[14px]','value' => 'text-[14px]'],
        };
    }

    /**
     * OTP Input sizes.
     *
     * @return array{cell:string, height:string, text:string, gap:string, radius:string}
     */
    public static function otp(string $size): array
    {
        return match ($size) {
            'xs'  => ['cell' => 'w-7',       'height' => 'h-8',      'text' => 'text-sm',      'gap' => 'gap-1.5', 'radius' => 'rounded-[var(--radius)]'],
            'sm'  => ['cell' => 'w-8',       'height' => 'h-10',     'text' => 'text-base',    'gap' => 'gap-2',   'radius' => 'rounded-[var(--radius)]'],
            'lg'  => ['cell' => 'w-12',      'height' => 'h-[56px]', 'text' => 'text-[22px]',  'gap' => 'gap-2',   'radius' => 'rounded-[var(--radius-lg)]'],
            'xl'  => ['cell' => 'w-14',      'height' => 'h-[68px]', 'text' => 'text-[28px]',  'gap' => 'gap-2.5', 'radius' => 'rounded-[var(--radius-lg)]'],
            '2xl' => ['cell' => 'w-[70px]',  'height' => 'h-[84px]', 'text' => 'text-[36px]',  'gap' => 'gap-3',   'radius' => 'rounded-[var(--radius-lg)]'],
            default => ['cell' => 'w-10',    'height' => 'h-[46px]', 'text' => 'text-[18px]',  'gap' => 'gap-2',   'radius' => 'rounded-[var(--radius)]'],
        };
    }

    /**
     * Select sizes (same as input but with extra right padding for the chevron).
     */
    public static function select(string $size): array
    {
        $base = static::input($size);
        $base['pr'] = match ($size) {
            'xs'  => 'pr-6',
            'sm'  => 'pr-7',
            'xl'  => 'pr-10',
            '2xl' => 'pr-12',
            default => 'pr-8',
        };
        return $base;
    }

    /**
     * All valid size values.
     */
    public static function valid(): array
    {
        return ['xs', 'sm', 'md', 'lg', 'xl', '2xl'];
    }

    /**
     * Resolve size from a context (component prop → global config → default).
     * Allows a global default to be set in config/jarenui.php.
     */
    public static function resolve(?string $size, string $fallback = 'md'): string
    {
        $size ??= config('jarenui.default_size', $fallback);
        return in_array($size, static::valid()) ? $size : $fallback;
    }
}
