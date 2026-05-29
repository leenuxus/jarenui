{{--
  Usage:
    <x-jaren::timeline>
        <x-jaren::timeline.item
            title="Repository created"
            time="2 days ago"
            status="done"
            icon="folder-plus"
        />
        <x-jaren::timeline.item
            title="CI pipeline running"
            time="Just now"
            status="active"
            icon="arrow-path"
        >
            Running 24 test cases across 3 suites…
        </x-jaren::timeline.item>
        <x-jaren::timeline.item
            title="Deploy to production"
            time="Scheduled"
            status="pending"
            icon="rocket-launch"
        />
    </x-jaren::timeline>
--}}

@props(['compact' => false])

<ol
    {{ $attributes->merge(['class' => 'flex flex-col gap-0']) }}
    role="list"
    aria-label="Timeline"
>
    {{ $slot }}
</ol>
