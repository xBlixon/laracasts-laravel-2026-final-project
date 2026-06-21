@use('App\IdeaStatus')

@props(['status' => IdeaStatus::PENDING])

@php
    $classes = 'inline-block rounded-full border px-2 py-1 text-xs font-medium ';

    $classes .= match ($status) {
        IdeaStatus::PENDING => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        IdeaStatus::IN_PROGRESS => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        IdeaStatus::COMPLETED => 'bg-primary/10 text-primary border-primary/20',
    };
@endphp

<span class="{{ $classes }}">
    {{ $status->label() }}
</span>
