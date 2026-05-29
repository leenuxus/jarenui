{{-- tabs/panel.blade.php --}}
@props(['name', 'class' => ''])

<div
    id="panel-{{ $name }}"
    role="tabpanel"
    :aria-labelledby="'tab-{{ $name }}'"
    :tabindex="activeTab === '{{ $name }}' ? 0 : -1"
    x-show="activeTab === '{{ $name }}'"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    {{ $attributes->merge(['class' => 'outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)] ' . $class]) }}
>
    {{ $slot }}
</div>

@once
<script>
function jarenTabs(defaultTab = null) {
    return {
        activeTab: defaultTab,
        variant: 'line',

        init() {
            // Auto-detect variant from tablist class
            const list = this.$el.querySelector('[role="tablist"]');
            if (list) {
                if (list.classList.contains('bg-[var(--bg2)]')) this.variant = 'pill';
                else if (list.classList.contains('border')) this.variant = 'box';
                else this.variant = 'line';
            }

            // Default to first tab if none specified
            if (!this.activeTab) {
                const first = this.$el.querySelector('[role="tab"]');
                if (first) {
                    // Extract name from id attribute: "tab-{name}"
                    const id = first.getAttribute(':id') || '';
                    this.activeTab = id.replace("'tab-", '').replace("'", '');
                }
            }
        },

        setTab(name) {
            this.activeTab = name;
            this.$dispatch('jaren-tab-change', { tab: name });
        },

        get tabs() {
            return [...this.$el.querySelectorAll('[role="tab"]:not([disabled])')];
        },

        nextTab() {
            const tabs = this.tabs;
            const idx  = tabs.findIndex(t => this.activeTab === t.getAttribute('\\:id')?.replace("'tab-", '').replace("'", ''));
            const next = tabs[(idx + 1) % tabs.length];
            if (next) { next.click(); next.focus(); }
        },

        prevTab() {
            const tabs = this.tabs;
            const idx  = tabs.findIndex(t => this.activeTab === t.getAttribute('\\:id')?.replace("'tab-", '').replace("'", ''));
            const prev = tabs[(idx - 1 + tabs.length) % tabs.length];
            if (prev) { prev.click(); prev.focus(); }
        },

        firstTab() {
            const t = this.tabs[0];
            if (t) { t.click(); t.focus(); }
        },

        lastTab() {
            const tabs = this.tabs;
            const t = tabs[tabs.length - 1];
            if (t) { t.click(); t.focus(); }
        },
    };
}
</script>
@endonce
