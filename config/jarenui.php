<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The theme class applied to <html> by default. This maps to the
    | `.theme-*` CSS classes defined in jarenui.css.
    |
    | Supported: null, "rose", "violet", "emerald", "amber", "sharp", "rounded"
    |
    */
    'default_theme' => env('JARENUI_THEME', null),

    /*
    |--------------------------------------------------------------------------
    | Accent Colour Override
    |--------------------------------------------------------------------------
    |
    | Override the --accent CSS variable globally. Accepts any CSS colour value.
    | If null, the default blue (#2563eb) defined in jarenui.css is used.
    |
    | Example: '#7c3aed'
    |
    */
    'accent_color' => env('JARENUI_ACCENT', null),

    /*
    |--------------------------------------------------------------------------
    | Dark Mode Strategy
    |--------------------------------------------------------------------------
    |
    | Controls how dark mode is detected and applied.
    |
    | "class"   — Tailwind-style .dark class on <html> (default)
    | "attribute" — data-theme="dark" attribute on <html>
    | "system"  — Follows prefers-color-scheme media query automatically
    |
    */
    'dark_mode' => env('JARENUI_DARK_MODE', 'attribute'),

    /*
    |--------------------------------------------------------------------------
    | CSS Injection
    |--------------------------------------------------------------------------
    |
    | When true, @jarenStyles injects a <link> tag pointing to the published
    | CSS file. Set to false if you're importing jarenui.css via your build
    | pipeline (Vite / Mix) instead.
    |
    */
    'inject_css' => env('JARENUI_INJECT_CSS', true),

    /*
    |--------------------------------------------------------------------------
    | Component Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix used for all Blade components.
    |
    | Default: "jaren"  →  <x-jaren::button>
    |
    | You can change this to avoid conflicts with other libraries,
    | but you'll need to re-publish views after changing.
    |
    */
    'prefix' => env('JARENUI_PREFIX', 'jaren'),

    /*
    |--------------------------------------------------------------------------
    | Toast Defaults
    |--------------------------------------------------------------------------
    |
    | Default configuration for the Toast notification system.
    |
    */
    'toast' => [
        'duration'  => 4000,   // auto-dismiss after ms (0 = persistent)
        'position'  => 'bottom-right',  // bottom-right | bottom-left | top-right | top-left
        'max_stack' => 5,       // maximum simultaneous toasts
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Defaults
    |--------------------------------------------------------------------------
    |
    | Default settings for the Table Livewire component.
    |
    */
    'table' => [
        'per_page'         => 10,
        'per_page_options' => [10, 25, 50, 100],
        'default_sort'     => 'id',
        'default_order'    => 'desc',
    ],

    /*
    |--------------------------------------------------------------------------
    | Border Radius
    |--------------------------------------------------------------------------
    |
    | Override the CSS radius tokens globally.
    | Accepts any CSS size value (px, rem, etc.)
    |
    */
    'radius' => [
        'sm' => env('JARENUI_RADIUS_SM', '6px'),
        'md' => env('JARENUI_RADIUS_MD', '10px'),
        'lg' => env('JARENUI_RADIUS_LG', '14px'),
    ],

];
