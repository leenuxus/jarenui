<?php

namespace JarenUI\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

/**
 * JarenUI Table — Generic reusable data table with sorting, searching, per-page.
 *
 * Extend this class in your own Livewire components:
 *
 *   class UsersTable extends \App\Livewire\Jaren\Table
 *   {
 *       public string $model = \App\Models\User::class;
 *
 *       public array $columns = [
 *           ['key' => 'name',       'label' => 'Name',    'sortable' => true],
 *           ['key' => 'email',      'label' => 'Email',   'sortable' => true],
 *           ['key' => 'created_at', 'label' => 'Joined',  'sortable' => true, 'format' => 'date'],
 *           ['key' => 'role',       'label' => 'Role'],
 *       ];
 *
 *       public array $searchable = ['name', 'email'];
 *   }
 *
 * Then in Blade: <livewire:users-table />
 */
abstract class Table extends Component
{
    use WithPagination;

    // ── Configuration (override in subclass) ──────────────────────────────────

    /** Eloquent model class */
    public string $model = '';

    /** Column definitions: [['key','label','sortable'?,'format'?,'class'?]] */
    public array $columns = [];

    /** Column keys that are searched with LIKE */
    public array $searchable = [];

    /** Default sort column */
    public string $sortColumn = 'id';

    /** Default sort direction */
    public string $sortDirection = 'desc';

    /** Rows per page options */
    public array $perPageOptions = [10, 25, 50, 100];

    // ── State ─────────────────────────────────────────────────────────────────

    public string $search = '';
    public int    $perPage = 10;
    public array  $selected = [];   // selected row IDs
    public bool   $selectAll = false;

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    // ── Sorting ───────────────────────────────────────────────────────────────

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn    = $column;
            $this->sortDirection = 'asc';
        }
    }

    // ── Selection ─────────────────────────────────────────────────────────────

    public function toggleSelectAll(): void
    {
        $this->selectAll = ! $this->selectAll;
        $this->selected  = $this->selectAll
            ? $this->getQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function toggleSelect(string|int $id): void
    {
        $id = (string) $id;
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_filter($this->selected, fn ($s) => $s !== $id));
        } else {
            $this->selected[] = $id;
        }
        $this->selectAll = false;
    }

    public function clearSelection(): void
    {
        $this->selected  = [];
        $this->selectAll = false;
    }

    // ── Query ─────────────────────────────────────────────────────────────────

    protected function getQuery()
    {
        $query = $this->model::query();

        // Search
        if ($this->search && !empty($this->searchable)) {
            $query->where(function ($q) {
                foreach ($this->searchable as $i => $col) {
                    $method = $i === 0 ? 'where' : 'orWhere';
                    $q->{$method}($col, 'like', '%' . $this->search . '%');
                }
            });
        }

        // Custom scopes (override in subclass)
        $this->applyFilters($query);

        return $query->orderBy($this->sortColumn, $this->sortDirection);
    }

    /** Override to add custom query scopes / eager loads */
    protected function applyFilters($query): void {}

    // ── Formatting helpers (used in blade) ───────────────────────────────────

    public function formatCell(mixed $value, string $format = ''): string
    {
        return match ($format) {
            'date'     => $value ? \Carbon\Carbon::parse($value)->format('M j, Y') : '—',
            'datetime' => $value ? \Carbon\Carbon::parse($value)->format('M j, Y H:i') : '—',
            'currency' => '$' . number_format((float) $value, 2),
            'bool'     => $value ? 'Yes' : 'No',
            default    => (string) ($value ?? ''),
        };
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('jarenui::components.jaren.table', [
            'rows'       => $this->getQuery()->paginate($this->perPage),
            'totalCount' => $this->getQuery()->count(),
        ]);
    }
}
