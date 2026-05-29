<?php

use JarenUI\JarenUI;
use JarenUI\Facades\JarenUI as JarenUIFacade;

// ── JarenUI core class ──────────────────────────────────────────────────────

test('JarenUI has a version', function () {
    $jaren = new JarenUI();
    expect($jaren->version())->toBeString()->not->toBeEmpty();
});

test('theme can be set and retrieved', function () {
    $jaren = new JarenUI();
    $jaren->theme('violet');
    expect($jaren->getTheme())->toBe('violet');
});

test('accent color can be set and retrieved', function () {
    $jaren = new JarenUI();
    $jaren->accent('#7c3aed');
    expect($jaren->getAccentColor())->toBe('#7c3aed');
});

test('renderStyles returns a stylesheet link', function () {
    $jaren = new JarenUI();
    $styles = $jaren->renderStyles();
    expect($styles)->toContain('vendor/jarenui/css/jarenui.css');
});

test('renderStyles includes accent override when set', function () {
    $jaren = new JarenUI();
    $jaren->accent('#7c3aed');
    expect($jaren->renderStyles())
        ->toContain('--accent:#7c3aed');
});

// ── Service Provider registration ─────────────────────────────────────────

test('service provider registers config', function () {
    expect(config('jarenui'))->toBeArray();
    expect(config('jarenui.toast.duration'))->toBe(4000);
    expect(config('jarenui.table.per_page'))->toBe(10);
});

test('facade resolves correctly', function () {
    expect(JarenUIFacade::version())->toBeString();
});

test('jarenui singleton is bound in container', function () {
    expect(app('jarenui'))->toBeInstanceOf(JarenUI::class);
});
