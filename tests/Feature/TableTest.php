<?php

use JarenUI\Livewire\Table;
use Livewire\Livewire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ── Test model & table setup ───────────────────────────────────────────────

beforeEach(function () {
    Schema::create('jaren_test_users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('role')->default('user');
        $table->timestamps();
    });

    JarenTestUser::create(['name' => 'Alice',   'email' => 'alice@test.com',   'role' => 'admin']);
    JarenTestUser::create(['name' => 'Bob',     'email' => 'bob@test.com',     'role' => 'user']);
    JarenTestUser::create(['name' => 'Charlie', 'email' => 'charlie@test.com', 'role' => 'user']);
    JarenTestUser::create(['name' => 'Diana',   'email' => 'diana@test.com',   'role' => 'admin']);
});

afterEach(function () {
    Schema::dropIfExists('jaren_test_users');
});

// Inline test model
class JarenTestUser extends Model
{
    protected $table    = 'jaren_test_users';
    protected $fillable = ['name', 'email', 'role'];
}

// Concrete table for testing
class JarenTestUsersTable extends Table
{
    public string $model = JarenTestUser::class;

    public array $columns = [
        ['key' => 'name',  'label' => 'Name',  'sortable' => true],
        ['key' => 'email', 'label' => 'Email', 'sortable' => true],
        ['key' => 'role',  'label' => 'Role'],
    ];

    public array $searchable = ['name', 'email'];
}

// ── Tests ──────────────────────────────────────────────────────────────────

test('Table renders all rows', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->assertStatus(200)
        ->assertSee('Alice')
        ->assertSee('Bob')
        ->assertSee('Charlie')
        ->assertSee('Diana');
});

test('Table filters rows by search', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->set('search', 'alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob')
        ->assertDontSee('Charlie');
});

test('Table email search works too', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->set('search', 'bob@test')
        ->assertSee('Bob')
        ->assertDontSee('Alice');
});

test('Table sorts ascending', function () {
    $component = Livewire::test(JarenTestUsersTable::class)
        ->call('sortBy', 'name');

    expect($component->get('sortColumn'))->toBe('name');
    expect($component->get('sortDirection'))->toBe('asc');
});

test('Table toggles to descending on second sort click', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', 'desc');
});

test('Table sort column changes when different column clicked', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'email')
        ->assertSet('sortColumn', 'email')
        ->assertSet('sortDirection', 'asc');
});

test('Table selects a single row', function () {
    $user = JarenTestUser::first();

    Livewire::test(JarenTestUsersTable::class)
        ->call('toggleSelect', $user->id)
        ->assertSet('selected', [(string) $user->id]);
});

test('Table deselects a row on second toggle', function () {
    $user = JarenTestUser::first();

    Livewire::test(JarenTestUsersTable::class)
        ->call('toggleSelect', $user->id)
        ->call('toggleSelect', $user->id)
        ->assertSet('selected', []);
});

test('Table selects all rows', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->call('toggleSelectAll')
        ->assertSet('selectAll', true)
        ->assertCount('selected', 4);
});

test('Table deselects all when toggleSelectAll called twice', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->call('toggleSelectAll')
        ->call('toggleSelectAll')
        ->assertSet('selectAll', false)
        ->assertSet('selected', []);
});

test('Table clears selection', function () {
    $user = JarenTestUser::first();

    Livewire::test(JarenTestUsersTable::class)
        ->call('toggleSelect', $user->id)
        ->call('clearSelection')
        ->assertSet('selected', [])
        ->assertSet('selectAll', false);
});

test('Table respects perPage setting', function () {
    $component = Livewire::test(JarenTestUsersTable::class)
        ->set('perPage', 2);

    // With 4 users and perPage=2, only 2 should be visible
    $viewData = $component->viewData('rows');
    expect($viewData->count())->toBe(2);
    expect($viewData->total())->toBe(4);
});

test('Table formatCell formats date correctly', function () {
    $table = new JarenTestUsersTable();
    $formatted = $table->formatCell('2024-01-15 10:30:00', 'date');
    expect($formatted)->toBe('Jan 15, 2024');
});

test('Table formatCell formats currency correctly', function () {
    $table = new JarenTestUsersTable();
    expect($table->formatCell('1234.5', 'currency'))->toBe('$1,234.50');
});

test('Table formatCell formats bool correctly', function () {
    $table = new JarenTestUsersTable();
    expect($table->formatCell(true,  'bool'))->toBe('Yes');
    expect($table->formatCell(false, 'bool'))->toBe('No');
});

test('Table resets page when search changes', function () {
    Livewire::test(JarenTestUsersTable::class)
        ->set('perPage', 2)
        ->call('nextPage') // go to page 2
        ->set('search', 'alice')
        ->assertSet('page', 1);
});
