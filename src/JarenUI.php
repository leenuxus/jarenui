<?php

namespace JarenUI;

/**
 * JarenUI — main singleton class.
 * Accessed via the JarenUI facade or app('jarenui').
 */
class JarenUI
{
    protected string $version = '1.1.0';

    /** Currently registered theme override (applied globally). */
    protected ?string $theme = null;

    /** Accent colour override. */
    protected ?string $accentColor = null;

    // ──────────────────────────────────────────────────────────────────────────

    public function version(): string
    {
        return $this->version;
    }

    /**
     * Set a global theme class (e.g. 'violet', 'rose', 'sharp').
     * The value is exposed to Blade via the $jarenTheme variable.
     */
    public function theme(string $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme ?? config('jarenui.default_theme');
    }

    /**
     * Override the --accent CSS variable globally.
     *
     * @param string $color  Any valid CSS colour (#hex, rgb(), hsl())
     */
    public function accent(string $color): static
    {
        $this->accentColor = $color;
        return $this;
    }

    public function getAccentColor(): ?string
    {
        return $this->accentColor ?? config('jarenui.accent_color');
    }

    /**
     * Generate the inline <style> block for the current configuration.
     * Called automatically by @jarenStyles.
     */
    public function renderStyles(): string
    {
        $vars = [];

        if ($color = $this->getAccentColor()) {
            $vars[] = "--accent:{$color}";
        }

        $style = $vars ? '<style>:root{' . implode(';', $vars) . '}</style>' : '';

        $cssUrl = asset('vendor/jarenui/css/jarenui.css');

        return $style . "\n<link rel=\"stylesheet\" href=\"{$cssUrl}\">";
    }
}
