<?php

namespace JarenUI\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * JarenUI AsyncCombobox — server-side searchable combobox.
 *
 * Extend this class to build server-side powered comboboxes:
 *
 *   class UserCombobox extends \JarenUI\Livewire\AsyncCombobox
 *   {
 *       public function search(string $query): array
 *       {
 *           return User::where('name', 'like', "%{$query}%")
 *               ->limit(10)
 *               ->get()
 *               ->map(fn ($u) => [
 *                   'value'    => $u->id,
 *                   'label'    => $u->name,
 *                   'meta'     => $u->email,
 *                   'initials' => $u->initials,
 *                   'color'    => $u->avatar_color,
 *               ])
 *               ->toArray();
 *       }
 *   }
 *
 * In Blade:
 *   <livewire:user-combobox wire:model="userId" label="Assign to"/>
 *
 * Or inline (no subclass needed):
 *   <livewire:jaren.async-combobox
 *       model="\App\Models\User"
 *       label-column="name"
 *       value-column="id"
 *       :searchable-columns="['name', 'email']"
 *       label="Assign to"
 *       wire:model="userId"
 *   />
 */
class AsyncCombobox extends Component
{
    // ── Configuration (override in subclass) ──────────────────────────────────

    /** Label shown above the input */
    public string $label = '';

    /** Hint shown below */
    public ?string $hint = null;

    /** Error shown below (set externally or via validation) */
    public ?string $error = null;

    /** Placeholder text */
    public string $placeholder = 'Search…';

    /** Allow selecting multiple values */
    public bool $multiple = false;

    /** Show avatar initials column */
    public bool $withAvatars = false;

    /** Show description column */
    public bool $withDescriptions = false;

    /** Show badge column */
    public bool $withBadges = false;

    /** Minimum characters before search fires */
    public int $minChars = 1;

    /** Max results returned */
    public int $limit = 10;

    /** Size: xs|sm|md|lg|xl|2xl */
    public string $size = 'md';

    // ── Inline Eloquent config (set these if not subclassing) ─────────────────

    /** Eloquent model class for inline use */
    public ?string $model = null;

    /** Column to use as the label */
    public string $labelColumn = 'name';

    /** Column to use as the value */
    public string $valueColumn = 'id';

    /** Columns searched with LIKE */
    public array $searchableColumns = ['name'];

    /** Column to use as description (optional) */
    public ?string $descriptionColumn = null;

    /** Column to use as badge (optional) */
    public ?string $badgeColumn = null;

    /** Column to use as initials (optional) */
    public ?string $initialsColumn = null;

    /** Column to use as avatar color (optional) */
    public ?string $colorColumn = null;

    // ── State ─────────────────────────────────────────────────────────────────

    /** Current search query */
    public string $query = '';

    /** Results returned from search() */
    public array $results = [];

    /** Whether a search is in progress */
    public bool $loading = false;

    /** Currently selected option(s) */
    public mixed $selected = null;   // single: ['value','label'] | multiple: []

    // ── Search ────────────────────────────────────────────────────────────────

    /**
     * Override in subclass to load results from any data source.
     * Must return array of: ['value', 'label', 'meta'?, 'description'?, 'badge'?, 'initials'?, 'color'?]
     *
     * @return array<int, array>
     */
    public function search(string $query): array
    {
        if (! $this->model || ! class_exists($this->model)) {
            return [];
        }

        $q = $this->model::query();

        // Search across configured columns
        $q->where(function ($builder) use ($query) {
            foreach ($this->searchableColumns as $i => $col) {
                $method = $i === 0 ? 'where' : 'orWhere';
                $builder->{$method}($col, 'like', "%{$query}%");
            }
        });

        return $q
            ->limit($this->limit)
            ->get()
            ->map(fn ($row) => array_filter([
                'value'       => $row->{$this->valueColumn},
                'label'       => $row->{$this->labelColumn},
                'description' => $this->descriptionColumn ? $row->{$this->descriptionColumn} : null,
                'badge'       => $this->badgeColumn       ? $row->{$this->badgeColumn}       : null,
                'initials'    => $this->initialsColumn    ? $row->{$this->initialsColumn}    : null,
                'color'       => $this->colorColumn       ? $row->{$this->colorColumn}       : null,
            ]))
            ->values()
            ->toArray();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function doSearch(string $query): void
    {
        $this->query = $query;

        if (mb_strlen($query) < $this->minChars) {
            $this->results = [];
            $this->loading = false;
            return;
        }

        $this->loading = true;
        $this->results = $this->search($query);
        $this->loading = false;
    }

    public function selectOption(mixed $value, string $label): void
    {
        $option = ['value' => $value, 'label' => $label];

        if ($this->multiple) {
            $exists = collect($this->selected ?? [])->contains('value', $value);
            if (! $exists) {
                $this->selected = array_merge($this->selected ?? [], [$option]);
            }
        } else {
            $this->selected = $option;
        }

        $this->dispatch('jaren-async-combobox-change', [
            'value'  => $this->multiple
                ? collect($this->selected)->pluck('value')->all()
                : $value,
            'option' => $this->selected,
        ]);
    }

    public function deselectOption(mixed $value): void
    {
        if ($this->multiple) {
            $this->selected = array_values(
                array_filter($this->selected ?? [], fn ($s) => $s['value'] != $value)
            );
        } else {
            $this->selected = null;
        }
    }

    public function clearAll(): void
    {
        $this->selected = $this->multiple ? [] : null;
        $this->results  = [];
        $this->query    = '';
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('jarenui::components.jaren.async-combobox');
    }
}
