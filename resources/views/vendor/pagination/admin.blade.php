{{-- Admin datatable footer: "Showing X–Y of Z" + pagination chips.
     Sits inside a .content-card (it draws its own top border). --}}
<div class="pagination-wrap">
    <div class="datatable-meta">
        @if ($paginator->total() > 0)
            <span>Showing <strong>{{ number_format($paginator->firstItem()) }}–{{ number_format($paginator->lastItem()) }}</strong> of <strong>{{ number_format($paginator->total()) }}</strong> {{ Str::plural('entry', $paginator->total()) }}</span>
        @else
            <span>No entries</span>
        @endif
    </div>

    @if ($paginator->hasPages())
        <nav class="pagination-buttons" role="navigation" aria-label="Pagination">
            @if ($paginator->onFirstPage())
                <span class="pagination-chip disabled" aria-hidden="true"><span class="icon" data-icon="chevron-left"></span></span>
            @else
                <a class="pagination-chip" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><span class="icon" data-icon="chevron-left"></span></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-chip disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-chip active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pagination-chip" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pagination-chip" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><span class="icon" data-icon="chevron-right"></span></a>
            @else
                <span class="pagination-chip disabled" aria-hidden="true"><span class="icon" data-icon="chevron-right"></span></span>
            @endif
        </nav>
    @endif
</div>
