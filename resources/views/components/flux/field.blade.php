{{--
  Field — a layout wrapper that adds label, hint, and error messaging
  to any form control (use when the control doesn't have these built-in).

  Usage:
    <x-jaren::field label="Date of birth" hint="YYYY-MM-DD" :error="$errors->first('dob')">
        <input type="date" class="inp" wire:model="dob">
    </x-jaren::field>

    <x-jaren::field label="Color" required>
        <x-jaren::color-picker wire:model="color"/>
    </x-jaren::field>
--}}

@props([
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
    'id'       => null,
    'inline'   => false,   // label left, input right
])

@php
$fieldId  = $id ?? 'field-' . \Illuminate\Support\Str::random(6);
$descId   = $fieldId . '-desc';
$outerClass = $inline
    ? 'flex items-start gap-3'
    : 'flex flex-col gap-1';
@endphp

<div
    {{ $attributes->only('class','wire:key')->merge(['class' => $outerClass]) }}
>
    @if($label)
        <label
            for="{{ $fieldId }}"
            class="{{ $inline ? 'pt-1.5 w-32 shrink-0' : '' }} text-xs font-medium text-[var(--text2)] leading-none"
        >
            {{ $label }}
            @if($required)
                <span class="text-[var(--danger-text)] ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="flex flex-col gap-1 flex-1 min-w-0">
        {{-- Clone the slot, injecting id + aria-describedby onto first focusable child --}}
        <div
            @if($error || $hint) aria-describedby="{{ $descId }}" @endif
            @if($error) data-invalid="true" @endif
        >
            {{ $slot }}
        </div>

        @if($error)
            <p id="{{ $descId }}" class="text-[11px] text-[var(--danger-text)] leading-tight" role="alert">
                <svg class="inline w-3 h-3 mr-0.5 -mt-px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                {{ $error }}
            </p>
        @elseif($hint)
            <p id="{{ $descId }}" class="text-[11px] text-[var(--text3)] leading-tight">
                {{ $hint }}
            </p>
        @endif
    </div>
</div>
