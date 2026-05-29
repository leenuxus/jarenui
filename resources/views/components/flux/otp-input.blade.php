@props([
    'digits'    => 6,
    'separator' => null,    // position to insert separator dash (e.g. 3 inserts after digit 3)
    'type'      => 'text',  // text | number | password
    'autofocus' => false,
    'id'        => null,
])

@php $baseId = $id ?? 'otp-' . \Illuminate\Support\Str::random(6); @endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => 'flex items-center gap-2']) }}
    x-data="jarenOtp({{ $digits }})"
    @paste.window="handlePaste($event)"
>
    @for($i = 0; $i < $digits; $i++)
        @if($separator && $i === $separator)
            <span class="text-[var(--text3)] text-xl font-medium select-none" aria-hidden="true">—</span>
        @endif

        <input
            id="{{ $baseId }}-{{ $i }}"
            type="{{ $type === 'number' ? 'tel' : $type }}"
            inputmode="{{ $type === 'number' ? 'numeric' : 'text' }}"
            pattern="{{ $type === 'number' ? '[0-9]*' : null }}"
            maxlength="1"
            @if($autofocus && $i === 0) autofocus @endif
            x-ref="digit{{ $i }}"
            x-model="digits[{{ $i }}]"
            @input="handleInput({{ $i }}, $event)"
            @keydown.backspace="handleBackspace({{ $i }}, $event)"
            @keydown.arrow-left.prevent="focus({{ $i }} - 1)"
            @keydown.arrow-right.prevent="focus({{ $i }} + 1)"
            @focus="$el.select()"
            class="w-10 h-12 text-center text-lg font-semibold font-mono
                bg-[var(--surface)] text-[var(--text)]
                border border-[var(--border2)] rounded-[var(--radius)]
                outline-none transition-all duration-100
                focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/20
                caret-transparent"
            :class="digits[{{ $i }}] ? 'border-[var(--accent-text)]' : ''"
            aria-label="Digit {{ $i + 1 }} of {{ $digits }}"
        >
    @endfor

    {{-- Hidden field for form submission --}}
    <input
        type="hidden"
        :name="'{{ $attributes->get('name', 'otp') }}'"
        :value="digits.join('')"
        {{ $attributes->except(['class','wire:key','digits','separator','type','autofocus','id','name']) }}
    >
</div>

@once
<script>
function jarenOtp(count) {
    return {
        digits: Array(count).fill(''),

        handleInput(index, event) {
            const val = event.target.value.replace(/[^0-9a-zA-Z]/g, '').slice(-1);
            this.digits[index] = val;
            if (val && index < count - 1) this.focus(index + 1);
            // Emit completed event when all filled
            if (this.digits.every(d => d !== '')) {
                this.$dispatch('jaren-otp-complete', { value: this.digits.join('') });
            }
        },

        handleBackspace(index, event) {
            if (!this.digits[index] && index > 0) {
                this.digits[index - 1] = '';
                this.focus(index - 1);
            }
        },

        handlePaste(event) {
            const text = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            if (!text) return;
            event.preventDefault();
            text.slice(0, count).split('').forEach((ch, i) => this.digits[i] = ch);
            this.focus(Math.min(text.length, count - 1));
        },

        focus(index) {
            const el = this.$refs[`digit${Math.max(0, Math.min(index, count - 1))}`];
            el?.focus();
        },
    };
}
</script>
@endonce
