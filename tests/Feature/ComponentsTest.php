<?php

use Illuminate\Support\Facades\Blade;

// ── Blade component rendering tests ───────────────────────────────────────
// These verify components render without exceptions and emit correct HTML.

test('Button renders with primary variant', function () {
    $html = Blade::render('<x-jaren::button>Save</x-jaren::button>');

    expect($html)
        ->toContain('Save')
        ->toContain('button');
});

test('Button renders as anchor when href provided', function () {
    $html = Blade::render('<x-jaren::button href="/dashboard">Go</x-jaren::button>');

    expect($html)
        ->toContain('<a')
        ->toContain('href="/dashboard"')
        ->toContain('Go');
});

test('Button renders loading state', function () {
    $html = Blade::render('<x-jaren::button :loading="true">Saving</x-jaren::button>');

    expect($html)->toContain('aria-busy="true"');
});

test('Badge renders with color class', function () {
    $html = Blade::render('<x-jaren::badge color="green">Active</x-jaren::badge>');

    expect($html)
        ->toContain('Active')
        ->toContain('span');
});

test('Badge renders dot indicator', function () {
    $html = Blade::render('<x-jaren::badge color="green" dot>Online</x-jaren::badge>');

    expect($html)->toContain('rounded-full');
});

test('Avatar renders initials from name', function () {
    $html = Blade::render('<x-jaren::avatar name="Jane Doe"/>');

    expect($html)
        ->toContain('JD')
        ->toContain('aria-label="Jane Doe"');
});

test('Avatar renders status dot', function () {
    $html = Blade::render('<x-jaren::avatar name="John" status="online"/>');

    expect($html)->toContain('av-status-dot');
});

test('Input renders with label', function () {
    $html = Blade::render('<x-jaren::input label="Email" name="email"/>');

    expect($html)
        ->toContain('Email')
        ->toContain('<label')
        ->toContain('<input');
});

test('Input renders error state', function () {
    $html = Blade::render('<x-jaren::input error="Required field" name="test"/>');

    expect($html)
        ->toContain('Required field')
        ->toContain('aria-invalid="true"');
});

test('Select renders native select element', function () {
    $html = Blade::render(
        '<x-jaren::select :options="[\'a\' => \'Option A\', \'b\' => \'Option B\']" name="choice"/>'
    );

    expect($html)
        ->toContain('<select')
        ->toContain('Option A')
        ->toContain('Option B');
});

test('Checkbox renders with label', function () {
    $html = Blade::render('<x-jaren::checkbox label="Accept terms" name="terms"/>');

    expect($html)
        ->toContain('Accept terms')
        ->toContain('type="checkbox"');
});

test('Switch renders toggle', function () {
    $html = Blade::render('<x-jaren::switch label="Dark mode" name="dark"/>');

    expect($html)
        ->toContain('Dark mode')
        ->toContain('role="switch"');
});

test('Callout renders with correct type', function () {
    $html = Blade::render('<x-jaren::callout type="success" title="Done!">All saved.</x-jaren::callout>');

    expect($html)
        ->toContain('Done!')
        ->toContain('All saved.')
        ->toContain('role="alert"');
});

test('Card renders title and body', function () {
    $html = Blade::render('<x-jaren::card title="My card">Body text</x-jaren::card>');

    expect($html)
        ->toContain('My card')
        ->toContain('Body text');
});

test('Modal renders with name and title', function () {
    $html = Blade::render('<x-jaren::modal name="test-modal"><x-slot:title>Delete?</x-slot:title>Are you sure?</x-jaren::modal>');

    expect($html)
        ->toContain('Delete?')
        ->toContain('Are you sure?')
        ->toContain('role="dialog"');
});

test('Breadcrumbs renders items', function () {
    $html = Blade::render('
        <x-jaren::breadcrumbs :items="[
            [\'label\' => \'Home\',     \'href\' => \'/\'],
            [\'label\' => \'Settings\', \'href\' => \'/settings\'],
            [\'label\' => \'Profile\'],
        ]"/>
    ');

    expect($html)
        ->toContain('Home')
        ->toContain('Settings')
        ->toContain('Profile')
        ->toContain('aria-current="page"');
});

test('Separator renders horizontal rule', function () {
    $html = Blade::render('<x-jaren::separator/>');

    expect($html)->toContain('role="separator"');
});

test('Separator renders with label', function () {
    $html = Blade::render('<x-jaren::separator label="or"/>');

    expect($html)->toContain('or');
});

test('Tooltip renders wrapper and trigger', function () {
    $html = Blade::render(
        '<x-jaren::tooltip content="Click me"><button>Trigger</button></x-jaren::tooltip>'
    );

    expect($html)
        ->toContain('Trigger')
        ->toContain('role="tooltip"')
        ->toContain('Click me');
});

test('Progress renders with value', function () {
    $html = Blade::render('<x-jaren::progress :value="68" label="Storage"/>');

    expect($html)
        ->toContain('Storage')
        ->toContain('aria-valuenow="68"')
        ->toContain('role="progressbar"');
});

test('Skeleton renders text variant', function () {
    $html = Blade::render('<x-jaren::skeleton variant="text" :lines="3"/>');

    expect($html)->toContain('rounded');
});

test('Heading renders correct tag level', function () {
    $html = Blade::render('<x-jaren::heading :level="1">Page Title</x-jaren::heading>');

    expect($html)
        ->toContain('<h1')
        ->toContain('Page Title');
});

test('Text renders paragraph', function () {
    $html = Blade::render('<x-jaren::text>Some description.</x-jaren::text>');

    expect($html)
        ->toContain('<p')
        ->toContain('Some description.');
});

test('OTP input renders correct number of inputs', function () {
    $html = Blade::render('<x-jaren::otp-input :digits="6"/>');

    expect(substr_count($html, 'otp-inp'))->toBe(6);
});

test('Brand renders name and dot icon', function () {
    $html = Blade::render('<x-jaren::brand name="Acme" dot/>');

    expect($html)
        ->toContain('Acme')
        ->toContain('<a');
});
