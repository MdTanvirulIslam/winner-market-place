@props(['items' => []])

{{-- items: ['Label' => url|null, ...] — the last entry is the current page. --}}
<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'mb-2']) }}>
    <ol class="flex flex-wrap items-center gap-2 text-[13px] text-muted">
        <li><a href="{{ route('admin.dashboard') }}" class="font-medium text-text hover:text-accent">Dashboard</a></li>
        @foreach($items as $label => $url)
            <li class="opacity-60"><span class="icon text-[10px]" data-icon="chevron-right"></span></li>
            @if($loop->last)
                <li class="font-semibold text-accent">{{ $label }}</li>
            @elseif($url)
                <li><a href="{{ $url }}" class="font-medium text-text hover:text-accent">{{ $label }}</a></li>
            @else
                <li class="font-medium">{{ $label }}</li>
            @endif
        @endforeach
    </ol>
</nav>
