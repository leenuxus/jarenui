{{--
  JarenUI Wizard — multi-step form shell.
  Rendered by JarenUI\Livewire\Wizard (subclass).

  Step panels are rendered via named slots:
    @foreach ($steps as $i => $step)
        @slot('step_'.$step['id'])
            ... your step content ...
        @endslot
    @endforeach

  Or: override render() in your subclass and return a custom view
  that extends this one.
--}}

@php
$sizes = [
    'sm' => ['wrap' => 'max-w-md',  'body' => 'p-5',    'dot' => 'w-6 h-6',  'dotText' => 'text-[11px]', 'label' => 'text-[10px]', 'title' => 'text-[14px]', 'sub' => 'text-[12px]', 'footer' => 'px-5 py-3'],
    'md' => ['wrap' => 'max-w-xl',  'body' => 'p-6',    'dot' => 'w-7 h-7',  'dotText' => 'text-[12px]', 'label' => 'text-[11px]', 'title' => 'text-[15px]', 'sub' => 'text-[13px]', 'footer' => 'px-6 py-4'],
    'lg' => ['wrap' => 'max-w-2xl', 'body' => 'p-7',    'dot' => 'w-8 h-8',  'dotText' => 'text-[13px]', 'label' => 'text-[12px]', 'title' => 'text-[17px]', 'sub' => 'text-[14px]', 'footer' => 'px-7 py-4'],
];
$sz = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    class="w-full {{ $sz['wrap'] }} bg-[var(--surface)] border border-[var(--border)] rounded-[var(--radius-xl)] overflow-hidden shadow-[var(--shadow)]"
    {{ $attributes->only('class', 'wire:key') }}
    role="region"
    aria-label="Multi-step form"
>

    {{-- ═══ STEPPER ════════════════════════════════════════════════════════════ --}}
    <div class="px-6 pt-5 pb-0 bg-[var(--surface)]">

        {{-- Numbered / icon stepper --}}
        @if($variant !== 'minimal')
        <nav aria-label="Form steps">
            <ol class="flex items-start relative" role="list">

                {{-- Connecting line (behind dots) --}}
                <div
                    class="absolute top-3.5 left-0 right-0 h-px bg-[var(--border)]"
                    aria-hidden="true"
                ></div>

                @foreach($steps as $i => $step)
                    @php
                        $isDone   = $i < $currentStep;
                        $isActive = $i === $currentStep;
                        $isPast   = $isDone && $clickable;
                    @endphp
                    <li
                        class="flex flex-col items-center flex-1 relative z-[1]
                            {{ $isPast ? 'cursor-pointer group' : 'cursor-default' }}"
                        @if($isPast) wire:click="goToStep({{ $i }})" @endif
                        role="listitem"
                        aria-current="{{ $isActive ? 'step' : 'false' }}"
                    >
                        {{-- Progress connector (coloured when done) --}}
                        @if($i < count($steps) - 1)
                            <div
                                class="absolute top-3.5 left-1/2 w-full h-px transition-colors duration-300
                                    {{ $isDone ? 'bg-[var(--success)]' : 'bg-[var(--border)]' }}"
                                aria-hidden="true"
                            ></div>
                        @endif

                        {{-- Dot --}}
                        <span
                            class="{{ $sz['dot'] }} flex items-center justify-center rounded-full border-2 transition-all duration-200 font-medium {{ $sz['dotText'] }} shrink-0
                                {{ $isDone
                                    ? 'bg-[var(--success)] border-[var(--success)] text-white'
                                    : ($isActive
                                        ? 'bg-[var(--accent)] border-[var(--accent)] text-white'
                                        : 'bg-[var(--surface)] border-[var(--border2)] text-[var(--text3)]') }}"
                            aria-hidden="true"
                        >
                            @if($isDone)
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            @elseif(!empty($step['icon']) && $showIcons)
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                    {{-- Icon resolved by name at runtime via x-dynamic-component in sub-templates --}}
                                </svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </span>

                        {{-- Label --}}
                        <span
                            class="{{ $sz['label'] }} mt-1.5 text-center leading-tight font-medium transition-colors
                                {{ $isDone
                                    ? 'text-[var(--success-text)]'
                                    : ($isActive
                                        ? 'text-[var(--accent-text)]'
                                        : 'text-[var(--text3)]') }}"
                        >
                            {{ $step['label'] }}
                        </span>
                    </li>
                @endforeach
            </ol>
        </nav>

        {{-- Optional linear progress bar --}}
        @if($showProgress)
            <div class="mt-4 h-1 bg-[var(--bg3)] rounded-full overflow-hidden">
                <div
                    class="h-full bg-[var(--accent)] rounded-full transition-all duration-500 ease-out"
                    style="width: {{ $this->progressPercent }}%"
                    role="progressbar"
                    aria-valuenow="{{ $this->progressPercent }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="Form progress"
                ></div>
            </div>
        @endif

        @elseif($variant === 'minimal')
        {{-- Minimal variant — just dots --}}
        <div class="flex items-center justify-center gap-2 pb-1" role="navigation" aria-label="Form steps">
            @foreach($steps as $i => $step)
                <button
                    type="button"
                    @if($i < $currentStep && $clickable) wire:click="goToStep({{ $i }})" @endif
                    class="transition-all duration-200 rounded-full
                        {{ $i === $currentStep ? 'w-5 h-2 bg-[var(--accent)]' : ($i < $currentStep ? 'w-2 h-2 bg-[var(--success)]' : 'w-2 h-2 bg-[var(--border2)]') }}"
                    aria-label="{{ $step['label'] }}"
                    aria-current="{{ $i === $currentStep ? 'step' : 'false' }}"
                ></button>
            @endforeach
        </div>
        @endif

        <div class="h-5"></div>
    </div>

    {{-- ═══ STEP BODY ══════════════════════════════════════════════════════════ --}}
    <div class="{{ $sz['body'] }}">

        {{-- Completed state --}}
        @if($completed)
            @if(isset($complete))
                {{ $complete }}
            @else
                <div class="text-center py-4">
                    <div class="w-14 h-14 rounded-full bg-[var(--success-bg)] flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-[var(--success)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                    </div>
                    <h2 class="{{ $sz['title'] }} font-medium text-[var(--text)] mb-2">All done!</h2>
                    <p class="{{ $sz['sub'] }} text-[var(--text2)]">Your form has been submitted successfully.</p>
                </div>
            @endif

        {{-- Cancelled state --}}
        @elseif($cancelled)
            @if(isset($cancelView))
                {{ $cancelView }}
            @else
                <div class="text-center py-4">
                    <p class="{{ $sz['sub'] }} text-[var(--text2)]">The form was cancelled.</p>
                </div>
            @endif

        {{-- Normal step rendering --}}
        @else
            @foreach($steps as $i => $step)
                @php $stepId = $step['id']; @endphp
                <div
                    @if($currentStep !== $i) style="display:none" @endif
                    role="tabpanel"
                    id="wizard-panel-{{ $stepId }}"
                    aria-labelledby="wizard-step-{{ $stepId }}"
                    wire:key="wizard-step-{{ $i }}"
                >
                    @if(isset($$stepId))
                        {{ $$stepId }}
                    @elseif(method_exists($this, 'render'.ucfirst($stepId)))
                        {!! $this->{'render'.ucfirst($stepId)}() !!}
                    @else
                        {{-- Fallback placeholder shown in base class --}}
                        <p class="{{ $sz['sub'] }} text-[var(--text3)] italic">
                            Step {{ $i + 1 }}: {{ $step['label'] }} — override the
                            <code class="text-[11px] bg-[var(--bg2)] px-1 py-px rounded">render{{ ucfirst($stepId) }}()</code>
                            method or pass a named slot <code class="text-[11px] bg-[var(--bg2)] px-1 py-px rounded">${{ $stepId }}</code>
                            to provide content for this step.
                        </p>
                    @endif
                </div>
            @endforeach

            {{-- Validation errors summary (shown at top of current step) --}}
            @if($errors->any())
                <div
                    class="mt-4 px-4 py-3 bg-[var(--danger-bg)] border border-[var(--danger)] rounded-[var(--radius-lg)] text-[var(--danger-text)]"
                    role="alert"
                    aria-live="polite"
                >
                    <p class="text-[13px] font-medium mb-1.5">Please fix the following:</p>
                    <ul class="text-[12px] list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

    </div>

    {{-- ═══ FOOTER ═════════════════════════════════════════════════════════════ --}}
    @unless($completed || $cancelled)
    <div
        class="{{ $sz['footer'] }} border-t border-[var(--border)] bg-[var(--bg2)] flex items-center justify-between gap-3"
    >
        {{-- Left: step counter + optional cancel --}}
        <div class="flex items-center gap-3">
            <span class="text-[12px] text-[var(--text3)]">
                Step {{ $currentStep + 1 }} of {{ count($steps) }}
            </span>

            {{-- Optional cancel slot --}}
            @if(isset($cancelButton))
                {{ $cancelButton }}
            @endif
        </div>

        {{-- Right: Back + Next/Submit --}}
        <div class="flex items-center gap-2">
            @unless($this->isFirstStep)
                <button
                    type="button"
                    wire:click="previous"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 h-8 px-4 text-[13px] font-medium rounded-[var(--radius)] border border-[var(--border2)] bg-[var(--surface)] text-[var(--text2)] hover:bg-[var(--bg3)] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)]"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-5 5 5 5"/></svg>
                    Back
                </button>
            @endunless

            <button
                type="button"
                wire:click="next"
                wire:loading.attr="disabled"
                wire:target="next"
                class="inline-flex items-center gap-1.5 h-8 px-5 text-[13px] font-medium rounded-[var(--radius)] bg-[var(--accent)] text-white hover:opacity-88 transition-opacity focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent)] focus-visible:ring-offset-2 disabled:opacity-45 disabled:cursor-not-allowed"
            >
                <span wire:loading wire:target="next">
                    <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="next">
                    {{ $this->isLastStep ? 'Submit' : 'Continue' }}
                </span>
                <svg
                    wire:loading.remove
                    wire:target="next"
                    class="w-3.5 h-3.5"
                    viewBox="0 0 20 20"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="m8 5 5 5-5 5"/>
                </svg>
            </button>
        </div>
    </div>
    @endunless

</div>
