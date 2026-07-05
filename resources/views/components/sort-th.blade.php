{{-- Sortable column header: toggles ?sort=field&dir=asc|desc, keeping the
     other filters and resetting to page 1. --}}
@props(['field', 'label'])

@php
    $isActive = request('sort') === $field;
    $dir = request('dir') === 'desc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery([
        'sort' => $field,
        'dir' => $isActive && $dir === 'asc' ? 'desc' : 'asc',
        'page' => null,
    ]);
@endphp

<th {{ $attributes }}>
    <a href="{{ $url }}" class="sort-link{{ $isActive ? ' active' : '' }}">
        {{ $label }}
        <span class="icon text-[9px]" data-icon="{{ $isActive ? ($dir === 'desc' ? 'arrow-down' : 'arrow-up') : 'chevrons-up-down' }}"></span>
    </a>
</th>
