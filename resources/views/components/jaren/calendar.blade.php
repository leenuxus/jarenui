@props([
    'value'          => null,      // Y-m-d | Y-m-d,Y-m-d (multiple) | Y-m-d/Y-m-d (range)
    'mode'           => 'single',  // single | multiple | range
    'min'            => null,      // Y-m-d | 'today'
    'max'            => null,      // Y-m-d | 'today'
    'unavailable'    => null,      // comma-separated Y-m-d list
    'size'           => 'base',    // xs | sm | base | lg | xl | 2xl
    'months'         => null,      // number of months to show (default: 1, or 2 for range)
    'minRange'       => null,      // minimum days selectable in range mode
    'maxRange'       => null,      // maximum days selectable in range mode
    'startDay'       => null,      // 0 (Sun) – 6 (Sat), null = user locale
    'withToday'      => false,     // show "Today" shortcut button
    'selectableHeader' => false,   // clicking month/year opens a month/year picker
    'fixedWeeks'     => false,     // always show 6 rows
    'weekNumbers'    => false,     // show ISO week numbers
    'openTo'         => null,      // Y-m-d — month to open to when no selection
    'forceOpenTo'    => false,     // always open to openTo, even when a date is selected
    'static'         => false,     // disable all interaction (display only)
    'navigation'     => true,      // show prev/next nav arrows
    'locale'         => null,      // BCP-47 locale string, e.g. 'fr', 'ja-JP'
])

@php
use Carbon\Carbon;

$today = Carbon::today();

// Resolve 'today' shorthand
$minDate = $min === 'today' ? $today->format('Y-m-d') : $min;
$maxDate = $max === 'today' ? $today->format('Y-m-d') : $max;

// Default months shown
$monthCount = $months ?? ($mode === 'range' ? 2 : 1);

// Unavailable dates array
$unavailableDates = $unavailable
    ? array_map('trim', explode(',', $unavailable))
    : [];

// Size → CSS modifier
$sizeClass = match($size) {
    'xs'  => 'jaren-cal--xs',
    'sm'  => 'jaren-cal--sm',
    'lg'  => 'jaren-cal--lg',
    'xl'  => 'jaren-cal--xl',
    '2xl' => 'jaren-cal--2xl',
    default => '',
};

$componentId = 'jaren-cal-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    id="{{ $componentId }}"
    {{ $attributes->only('class', 'wire:key')->merge(['class' => 'inline-block']) }}
    x-data="jarenCalendar({
        mode:            '{{ $mode }}',
        value:           @js($value),
        minDate:         @js($minDate),
        maxDate:         @js($maxDate),
        unavailable:     @js($unavailableDates),
        months:          {{ $monthCount }},
        minRange:        @js($minRange ? (int)$minRange : null),
        maxRange:        @js($maxRange ? (int)$maxRange : null),
        startDay:        @js($startDay !== null ? (int)$startDay : null),
        locale:          @js($locale),
        withToday:       {{ $withToday ? 'true' : 'false' }},
        selectableHeader:{{ $selectableHeader ? 'true' : 'false' }},
        fixedWeeks:      {{ $fixedWeeks ? 'true' : 'false' }},
        weekNumbers:     {{ $weekNumbers ? 'true' : 'false' }},
        openTo:          @js($openTo),
        forceOpenTo:     {{ $forceOpenTo ? 'true' : 'false' }},
        isStatic:        {{ $static ? 'true' : 'false' }},
        showNavigation:  {{ $navigation ? 'true' : 'false' }},
        wireModelId:     '{{ $attributes->whereStartsWith('wire:model')->first() ? $componentId : '' }}',
    })"
    @if(!$static)
        x-on:keydown.left.prevent="prevMonth()"
        x-on:keydown.right.prevent="nextMonth()"
    @endif
    role="application"
    aria-label="Calendar"
>
    {{-- Month panels --}}
    <div class="flex" :class="months > 1 ? 'divide-x divide-[var(--jaren-border)]' : ''">
        <template x-for="(panel, pi) in visiblePanels" :key="pi">
            <div
                class="jaren-calendar {{ $sizeClass }}"
                :class="{ 'jaren-calendar--static': isStatic }"
            >
                {{-- ── Header ──────────────────────────────────────────────── --}}
                <div class="jaren-cal-header">
                    {{-- Prev nav (only on first panel) --}}
                    <button
                        x-show="showNavigation && pi === 0"
                        type="button"
                        @click="prevMonth()"
                        class="jaren-cal-nav-btn"
                        aria-label="Previous month"
                        :disabled="isStatic"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m12 5-5 5 5 5"/>
                        </svg>
                    </button>
                    <div x-show="showNavigation && pi !== 0" class="w-7"></div>

                    {{-- Title --}}
                    <button
                        type="button"
                        class="jaren-cal-title"
                        :class="selectableHeader ? 'cursor-pointer hover:text-[var(--jaren-accent)] transition-colors' : 'cursor-default'"
                        @click="selectableHeader && openHeaderPicker(pi)"
                        x-text="panel.titleStr"
                        aria-live="polite"
                    ></button>

                    {{-- Next nav (only on last panel) --}}
                    <button
                        x-show="showNavigation && pi === visiblePanels.length - 1"
                        type="button"
                        @click="nextMonth()"
                        class="jaren-cal-nav-btn"
                        aria-label="Next month"
                        :disabled="isStatic"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m8 5 5 5-5 5"/>
                        </svg>
                    </button>
                    <div x-show="showNavigation && pi !== visiblePanels.length - 1" class="w-7"></div>
                </div>

                {{-- ── Header picker (month/year grid) ─────────────────────── --}}
                <div
                    x-show="selectableHeader && headerPickerOpen === pi"
                    x-transition
                    class="jaren-cal-picker-overlay"
                >
                    <div class="jaren-cal-picker-grid">
                        <template x-for="(m, mi) in monthNames" :key="mi">
                            <button
                                type="button"
                                @click="selectHeaderMonth(pi, mi)"
                                class="jaren-cal-picker-cell"
                                :class="panel.month === mi ? 'jaren-cal-picker-cell--active' : ''"
                                x-text="m"
                            ></button>
                        </template>
                    </div>
                    <div class="jaren-cal-picker-year-row">
                        <button type="button" class="jaren-cal-nav-btn" @click="pickerYear--" aria-label="Previous year">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m12 5-5 5 5 5"/></svg>
                        </button>
                        <span class="jaren-cal-title" x-text="pickerYear"></span>
                        <button type="button" class="jaren-cal-nav-btn" @click="pickerYear++" aria-label="Next year">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m8 5 5 5-5 5"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ── Day-of-week row ───────────────────────────────────────── --}}
                <div class="jaren-cal-grid">
                    <div class="jaren-cal-dow-row">
                        <template x-if="weekNumbers">
                            <div class="jaren-cal-dow-cell jaren-cal-week-num-header" aria-hidden="true">#</div>
                        </template>
                        <template x-for="day in panel.dayNames" :key="day">
                            <div class="jaren-cal-dow-cell" aria-hidden="true" x-text="day"></div>
                        </template>
                    </div>

                    {{-- ── Day cells ────────────────────────────────────────── --}}
                    <div class="jaren-cal-cells">
                        <template x-for="(week, wi) in panel.weeks" :key="wi">
                            <div class="jaren-cal-week-row">
                                <template x-if="weekNumbers">
                                    <div class="jaren-cal-week-num" x-text="week.number" aria-hidden="true"></div>
                                </template>
                                <template x-for="(cell, ci) in week.days" :key="ci">
                                    <button
                                        type="button"
                                        :disabled="cell.disabled || isStatic"
                                        @click="!cell.disabled && !isStatic && selectDate(cell.date)"
                                        @mouseenter="mode === 'range' && setHoverDate(cell.date)"
                                        @mouseleave="mode === 'range' && setHoverDate(null)"
                                        class="jaren-cal-cell"
                                        :class="{
                                            'jaren-cal-cell--other':    cell.otherMonth,
                                            'jaren-cal-cell--today':    cell.isToday,
                                            'jaren-cal-cell--selected': cell.selected,
                                            'jaren-cal-cell--range-start': cell.rangeStart,
                                            'jaren-cal-cell--range-end':   cell.rangeEnd,
                                            'jaren-cal-cell--in-range':    cell.inRange,
                                            'jaren-cal-cell--disabled':    cell.disabled,
                                            'jaren-cal-cell--unavailable': cell.unavailable,
                                        }"
                                        :aria-selected="cell.selected ? 'true' : 'false'"
                                        :aria-disabled="cell.disabled ? 'true' : 'false'"
                                        :aria-label="cell.ariaLabel"
                                        x-text="cell.day"
                                    ></button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── Footer (Today shortcut) ──────────────────────────────── --}}
                @if($withToday)
                    <div class="jaren-cal-footer" x-show="pi === visiblePanels.length - 1">
                        <button
                            type="button"
                            class="jaren-cal-today-btn"
                            @click="goToToday()"
                        >
                            Today
                        </button>
                    </div>
                @endif
            </div>
        </template>
    </div>

    {{-- Hidden input for wire:model / form submission --}}
    <input
        type="hidden"
        {{ $attributes->whereStartsWith('wire:model') }}
        :value="serializedValue"
        {{ $attributes->whereStartsWith('name') }}
    >
</div>

{{-- ── Styles (scoped via .jaren-calendar) ────────────────────────────────── --}}
@once
<style>
:root {
    --jaren-accent:       #2563eb;
    --jaren-accent-bg:    #eff6ff;
    --jaren-accent-text:  #1d4ed8;
    --jaren-border:       rgba(0,0,0,0.08);
    --jaren-text:         var(--color-text-primary, #1a1917);
    --jaren-text-muted:   var(--color-text-secondary, #6b7280);
    --jaren-text-faint:   var(--color-text-tertiary, #9ca3af);
    --jaren-surface:      var(--color-background-primary, #fff);
    --jaren-surface2:     var(--color-background-secondary, #f9f9f8);
    --jaren-radius:       8px;
    --jaren-cell:         34px;
    --jaren-font:         13px;
}

.jaren-calendar {
    background: var(--jaren-surface);
    border: 0.5px solid var(--jaren-border);
    border-radius: var(--jaren-radius);
    overflow: hidden;
    display: inline-flex;
    flex-direction: column;
    user-select: none;
    position: relative;
}

/* ── Header ──────────────────────────────────────────────────────────────── */
.jaren-cal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px 6px;
    border-bottom: 0.5px solid var(--jaren-border);
    gap: 4px;
}
.jaren-cal-title {
    font-size: var(--jaren-font);
    font-weight: 500;
    color: var(--jaren-text);
    background: transparent;
    border: none;
    flex: 1;
    text-align: center;
    padding: 2px 4px;
    border-radius: 4px;
}
.jaren-cal-nav-btn {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 0.5px solid var(--jaren-border);
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--jaren-text-muted);
    transition: background .1s;
    flex-shrink: 0;
}
.jaren-cal-nav-btn:hover:not(:disabled) { background: var(--jaren-surface2); }
.jaren-cal-nav-btn:disabled { opacity: .35; cursor: not-allowed; }

/* ── Grid ────────────────────────────────────────────────────────────────── */
.jaren-cal-grid { padding: 6px 8px 8px; }
.jaren-cal-dow-row {
    display: flex;
    margin-bottom: 2px;
}
.jaren-cal-dow-cell {
    width: var(--jaren-cell);
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 500;
    color: var(--jaren-text-muted);
}
.jaren-cal-cells { display: flex; flex-direction: column; gap: 1px; }
.jaren-cal-week-row { display: flex; }

/* ── Day cells ───────────────────────────────────────────────────────────── */
.jaren-cal-cell {
    width: var(--jaren-cell);
    height: var(--jaren-cell);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--jaren-font);
    border-radius: 6px;
    cursor: pointer;
    color: var(--jaren-text);
    background: transparent;
    border: none;
    transition: background .1s, color .1s;
    position: relative;
}
.jaren-cal-cell:hover:not(:disabled):not(.jaren-cal-cell--selected):not(.jaren-cal-cell--in-range) {
    background: var(--jaren-surface2);
}
.jaren-cal-cell--other { color: var(--jaren-text-faint); }
.jaren-cal-cell--today { font-weight: 500; color: var(--jaren-accent); }
.jaren-cal-cell--today::after {
    content: '';
    position: absolute;
    bottom: 3px;
    left: 50%;
    transform: translateX(-50%);
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: var(--jaren-accent);
}
.jaren-cal-cell--selected {
    background: var(--jaren-accent);
    color: #fff;
    font-weight: 500;
}
.jaren-cal-cell--selected::after { display: none; }
.jaren-cal-cell--range-start { border-radius: 6px 0 0 6px; }
.jaren-cal-cell--range-end   { border-radius: 0 6px 6px 0; }
.jaren-cal-cell--range-start.jaren-cal-cell--range-end { border-radius: 6px; }
.jaren-cal-cell--in-range {
    background: var(--jaren-accent-bg);
    color: var(--jaren-accent-text);
    border-radius: 0;
}
.jaren-cal-cell--in-range:hover { background: var(--jaren-accent-bg); }
.jaren-cal-cell--disabled,
.jaren-cal-cell--unavailable {
    opacity: .3;
    cursor: not-allowed;
    pointer-events: none;
}
.jaren-calendar--static .jaren-cal-cell { cursor: default; pointer-events: none; }

/* ── Week numbers ────────────────────────────────────────────────────────── */
.jaren-cal-week-num,
.jaren-cal-week-num-header {
    width: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: var(--jaren-text-faint);
    border-right: 0.5px solid var(--jaren-border);
    margin-right: 4px;
}
.jaren-cal-week-num { height: var(--jaren-cell); }

/* ── Header picker overlay ───────────────────────────────────────────────── */
.jaren-cal-picker-overlay {
    position: absolute;
    inset: 0;
    background: var(--jaren-surface);
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 8px;
}
.jaren-cal-picker-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    flex: 1;
}
.jaren-cal-picker-cell {
    padding: 6px 4px;
    border-radius: 6px;
    border: none;
    background: transparent;
    font-size: 12px;
    cursor: pointer;
    color: var(--jaren-text);
    transition: background .1s;
}
.jaren-cal-picker-cell:hover { background: var(--jaren-surface2); }
.jaren-cal-picker-cell--active {
    background: var(--jaren-accent);
    color: #fff;
    font-weight: 500;
}
.jaren-cal-picker-year-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 0 0;
    border-top: 0.5px solid var(--jaren-border);
    margin-top: 4px;
}

/* ── Footer ──────────────────────────────────────────────────────────────── */
.jaren-cal-footer {
    padding: 6px 8px;
    border-top: 0.5px solid var(--jaren-border);
    display: flex;
    justify-content: center;
}
.jaren-cal-today-btn {
    height: 26px;
    padding: 0 12px;
    border-radius: 6px;
    border: 0.5px solid var(--jaren-border);
    background: transparent;
    font-size: 12px;
    cursor: pointer;
    color: var(--jaren-accent-text);
    font-weight: 500;
    transition: background .1s;
}
.jaren-cal-today-btn:hover { background: var(--jaren-accent-bg); }

/* ── Size variants ───────────────────────────────────────────────────────── */
.jaren-cal--xs { --jaren-cell: 26px; --jaren-font: 11px; }
.jaren-cal--sm { --jaren-cell: 30px; --jaren-font: 12px; }
.jaren-cal--lg { --jaren-cell: 40px; --jaren-font: 14px; }
.jaren-cal--xl { --jaren-cell: 46px; --jaren-font: 15px; }
.jaren-cal--2xl{ --jaren-cell: 52px; --jaren-font: 16px; }

/* ── Dark mode ────────────────────────────────────────────────────────────── */
[data-theme="dark"] .jaren-calendar,
.dark .jaren-calendar {
    --jaren-accent:      #3b82f6;
    --jaren-accent-bg:   #172554;
    --jaren-accent-text: #93c5fd;
    --jaren-border:      rgba(255,255,255,0.09);
}
</style>
@endonce

{{-- ── Alpine.js component ─────────────────────────────────────────────────── --}}
@once
<script>
function jarenCalendar(config) {
    return {
        mode:            config.mode || 'single',
        minDate:         config.minDate || null,
        maxDate:         config.maxDate || null,
        unavailable:     config.unavailable || [],
        months:          config.months || 1,
        minRange:        config.minRange || null,
        maxRange:        config.maxRange || null,
        startDay:        config.startDay,
        locale:          config.locale || null,
        withToday:       config.withToday || false,
        selectableHeader:config.selectableHeader || false,
        fixedWeeks:      config.fixedWeeks || false,
        weekNumbers:     config.weekNumbers || false,
        isStatic:        config.isStatic || false,
        showNavigation:  config.showNavigation !== false,

        // internal state
        viewYear:   0,
        viewMonth:  0,
        selected:   null,   // single → 'Y-m-d'; multiple → []; range → {start,end}
        hoverDate:  null,
        headerPickerOpen: -1,
        pickerYear: 0,

        // ── lifecycle ────────────────────────────────────────────────────────
        init() {
            const today = new Date(); today.setHours(0,0,0,0);

            // Parse initial value
            this.selected = this._parseValue(config.value);

            // Determine open-to month
            const openTo = (config.forceOpenTo || !config.value) && config.openTo
                ? this._parseDate(config.openTo)
                : this._getInitialDate();

            this.viewYear  = openTo.getFullYear();
            this.viewMonth = openTo.getMonth();
            this.pickerYear = this.viewYear;
        },

        // ── computed ─────────────────────────────────────────────────────────
        get monthNames() {
            return Array.from({length:12},(_,i)=>
                new Date(2000,i,1).toLocaleString(this.locale||undefined,{month:'short'}));
        },

        get visiblePanels() {
            return Array.from({length:this.months},(_,i)=>{
                let m = this.viewMonth + i;
                let y = this.viewYear;
                while(m>11){m-=12;y++;}
                return this._buildPanel(y, m);
            });
        },

        get serializedValue() {
            if(!this.selected) return '';
            if(this.mode==='multiple') return Array.isArray(this.selected) ? this.selected.join(',') : '';
            if(this.mode==='range'){
                const {start,end}=this.selected||{};
                return start&&end ? `${start}/${end}` : start||'';
            }
            return this.selected||'';
        },

        // ── navigation ───────────────────────────────────────────────────────
        prevMonth() {
            if(this.viewMonth===0){this.viewMonth=11;this.viewYear--;} else {this.viewMonth--;}
        },
        nextMonth() {
            if(this.viewMonth===11){this.viewMonth=0;this.viewYear++;} else {this.viewMonth++;}
        },
        goToToday() {
            const t=new Date(); t.setHours(0,0,0,0);
            this.viewYear=t.getFullYear(); this.viewMonth=t.getMonth();
            if(!this.isStatic && this.mode==='single') this.selectDate(this._fmt(t));
        },
        openHeaderPicker(pi) {
            this.headerPickerOpen = this.headerPickerOpen===pi ? -1 : pi;
            this.pickerYear = this.viewYear;
        },
        selectHeaderMonth(pi, mi) {
            let targetMonth = this.viewMonth + pi;
            let targetYear  = this.viewYear;
            while(targetMonth>11){targetMonth-=12;targetYear++;}
            // Adjust viewMonth/viewYear so panel pi shows month mi of pickerYear
            this.viewMonth  = mi - pi;
            this.viewYear   = this.pickerYear;
            while(this.viewMonth<0){this.viewMonth+=12;this.viewYear--;}
            while(this.viewMonth>11){this.viewMonth-=12;this.viewYear++;}
            this.headerPickerOpen = -1;
        },

        // ── selection ────────────────────────────────────────────────────────
        selectDate(dateStr) {
            if(this.mode==='single'){
                this.selected = this.selected===dateStr ? null : dateStr;
                this.$dispatch('jaren-calendar-change', {value: this.selected});

            } else if(this.mode==='multiple'){
                if(!Array.isArray(this.selected)) this.selected=[];
                const idx = this.selected.indexOf(dateStr);
                if(idx>=0) this.selected.splice(idx,1);
                else       this.selected.push(dateStr);
                this.selected = [...this.selected];
                this.$dispatch('jaren-calendar-change', {value: this.selected});

            } else if(this.mode==='range'){
                const cur = this.selected||{};
                if(!cur.start || cur.end){
                    // start new range
                    this.selected = {start: dateStr, end: null};
                } else {
                    const s=new Date(cur.start), d=new Date(dateStr);
                    if(d<s){
                        // clicked before start → restart
                        this.selected = {start: dateStr, end: null};
                    } else {
                        // complete range
                        const len = Math.round((d-s)/(1000*60*60*24));
                        if(this.minRange && len < this.minRange) return;
                        if(this.maxRange && len > this.maxRange) return;
                        this.selected = {start: cur.start, end: dateStr};
                        this.$dispatch('jaren-calendar-change', {value: this.selected});
                    }
                }
            }
        },
        setHoverDate(d){ this.hoverDate = d; },

        // ── panel builder ────────────────────────────────────────────────────
        _buildPanel(year, month) {
            const locale      = this.locale || undefined;
            const titleStr    = new Date(year,month,1).toLocaleString(locale,{month:'long',year:'numeric'});
            const firstDow    = this.startDay !== null && this.startDay !== undefined
                ? this.startDay
                : this._detectStartDay();

            // Day-of-week headers
            const dayNames = Array.from({length:7},(_,i)=>{
                const d=new Date(2017,0,1+((i+firstDow)%7));
                return d.toLocaleString(locale,{weekday:'short'}).slice(0,2);
            });

            // Build weeks
            const today = new Date(); today.setHours(0,0,0,0);
            const firstOfMonth  = new Date(year,month,1);
            const lastOfMonth   = new Date(year,month+1,0);
            let cursor = new Date(firstOfMonth);
            cursor.setDate(cursor.getDate() - ((cursor.getDay()-firstDow+7)%7));

            const weeks = [];
            while(cursor<=lastOfMonth || (this.fixedWeeks && weeks.length<6)){
                if(weeks.length>=6) break;
                const week={number: this._isoWeek(cursor), days:[]};
                for(let di=0;di<7;di++){
                    const d=new Date(cursor);
                    const dStr=this._fmt(d);
                    const inMonth=d.getMonth()===month && d.getFullYear()===year;
                    week.days.push({
                        day:        d.getDate(),
                        date:       dStr,
                        otherMonth: !inMonth,
                        isToday:    d.getTime()===today.getTime(),
                        disabled:   this._isDisabled(d),
                        unavailable:this.unavailable.includes(dStr),
                        selected:   this._isSelected(dStr),
                        rangeStart: this._isRangeStart(dStr),
                        rangeEnd:   this._isRangeEnd(dStr),
                        inRange:    this._isInRange(d),
                        ariaLabel:  d.toLocaleDateString(locale,{weekday:'long',year:'numeric',month:'long',day:'numeric'}),
                    });
                    cursor.setDate(cursor.getDate()+1);
                }
                weeks.push(week);
            }

            return {year, month, titleStr, dayNames, weeks};
        },

        // ── helpers ──────────────────────────────────────────────────────────
        _fmt(d){
            return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        },
        _parseDate(str){
            if(!str) return new Date();
            const d=new Date(str+'T00:00:00'); d.setHours(0,0,0,0); return d;
        },
        _parseValue(val){
            if(!val) return this.mode==='multiple' ? [] : this.mode==='range' ? null : null;
            if(this.mode==='multiple') return val.split(',').map(s=>s.trim());
            if(this.mode==='range'){
                const [s,e]=(val||'').split('/');
                return {start:s?.trim()||null,end:e?.trim()||null};
            }
            return val;
        },
        _getInitialDate(){
            if(this.mode==='range' && this.selected?.start) return this._parseDate(this.selected.start);
            if(this.mode==='multiple' && this.selected?.length) return this._parseDate(this.selected[0]);
            if(this.mode==='single' && this.selected) return this._parseDate(this.selected);
            return new Date();
        },
        _isDisabled(d){
            const s=this._fmt(d);
            if(this.minDate && s<this.minDate) return true;
            if(this.maxDate && s>this.maxDate) return true;
            if(this.unavailable.includes(s))   return true;
            return false;
        },
        _isSelected(dStr){
            if(this.mode==='multiple') return Array.isArray(this.selected) && this.selected.includes(dStr);
            if(this.mode==='range'){
                const {start,end}=this.selected||{};
                return dStr===start || dStr===end;
            }
            return dStr===this.selected;
        },
        _isRangeStart(dStr){
            if(this.mode!=='range') return false;
            return dStr===(this.selected?.start);
        },
        _isRangeEnd(dStr){
            if(this.mode!=='range') return false;
            return dStr===(this.selected?.end);
        },
        _isInRange(d){
            if(this.mode!=='range') return false;
            const {start,end}=this.selected||{};
            const endDate = end
                ? new Date(end+'T00:00:00')
                : (this.hoverDate ? new Date(this.hoverDate+'T00:00:00') : null);
            if(!start || !endDate) return false;
            const s=new Date(start+'T00:00:00');
            return d>s && d<endDate;
        },
        _detectStartDay(){
            try {
                const loc=this.locale||undefined;
                const info=new Intl.Locale(loc||navigator.language||'en-US');
                return info.getWeekInfo?.()?.firstDay ?? info.weekInfo?.firstDay ?? 0;
            } catch{ return 0; }
        },
        _isoWeek(d){
            const t=new Date(d); t.setHours(0,0,0,0);
            t.setDate(t.getDate()+3-(t.getDay()+6)%7);
            const w=new Date(t.getFullYear(),0,4);
            return 1+Math.round(((t-w)/86400000-3+(w.getDay()+6)%7)/7);
        },
    };
}
</script>
@endonce
