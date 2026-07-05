{{-- Datatable toolbar: search on the left, filter selects + per-page +
     reset on the right. Extra filters go in the slot (give selects
     data-autosubmit so they apply on change). --}}
@props(['action', 'searchPlaceholder' => 'Search…', 'search' => true])

<form method="GET" action="{{ $action }}" class="datatable-toolbar mb-0">
    {{-- Keep the active sort when searching/filtering. --}}
    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
        <input type="hidden" name="dir" value="{{ request('dir', 'asc') }}">
    @endif

    @if($search)
        <div class="datatable-search">
            <span class="icon" data-icon="search"></span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ $searchPlaceholder }}" aria-label="Search">
        </div>
    @endif

    <div class="datatable-filters">
        {{ $slot }}

        <select class="panel-select w-auto py-2.5 text-[13px]" name="per_page" data-autosubmit aria-label="Rows per page">
            @foreach([10, 20, 50] as $n)
                <option value="{{ $n }}" @selected((int) request('per_page', 20) === $n)>{{ $n }} / page</option>
            @endforeach
        </select>

        @if(request()->query())
            <a href="{{ $action }}" class="filter-chip">Reset</a>
        @endif
    </div>
</form>
