{{--
  Radio group wrapper.

  Usage:
    <x-jaren::radio-group label="Billing cycle" wire:model="billing" variant="card">
        <x-jaren::radio value="monthly"  label="Monthly"  description="$12/month" />
        <x-jaren::radio value="annual"   label="Annual"   description="$99/year · Save 30%" />
        <x-jaren::radio value="lifetime" label="Lifetime" description="$299 once" :disabled="true"/>
    </x-jaren::radio-group>
--}}

@props([
    'label'       => null,
    'hint'        => null,
    'error'       => null,
    'name'        => null,
    'variant'     => 'default',   // default | card | inline
    'orientation' => 'vertical',  // vertical | horizontal
    'id'          => null,
])

@php
$groupId = $id ?? 'radio-group-' . \Illuminate\Support\Str::random(6);

$wrapClass = match($variant) {
    'card'   => match($orientation) {
        'horizontal' => 'flex flex-wrap gap-2',
        default      => 'flex flex-col gap-2',
    },
    'inline' => 'flex flex-wrap gap-x-4 gap-y-2',
    default  => match($orientation) {
        'horizontal' => 'flex flex-wrap gap-x-4 gap-y-2',
        default      => 'flex flex-col gap-2',
    },
};
@endphp

<div
    {{ $attributes->only('class','wire:key','wire:model')->merge(['class' => 'flex flex-col gap-1.5']) }}
    role="radiogroup"
    aria-labelledby="{{ $groupId }}-label"
    @if($error) aria-describedby="{{ $groupId }}-err" @endif
>
    @if($label)
        <span id="{{ $groupId }}-label" class="text-xs font-medium text-[var(--text2)] leading-none">
            {{ $label }}
        </span>
    @endif

    <div class="{{ $wrapClass }}">
        {{ $slot }}
    </div>

    @if($error)
        <p id="{{ $groupId }}-err" class="text-[11px] text-[var(--danger-text)]" role="alert">
            {{ $error }}
        </p>
    @elseif($hint)
        <p class="text-[11px] text-[var(--text3)]">{{ $hint }}</p>
    @endif
</div>
