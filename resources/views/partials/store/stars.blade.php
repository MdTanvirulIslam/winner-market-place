{{-- $rating: 0-5 (float ok). Filled stars in amber, the rest muted. --}}
<span class="inline-flex items-center gap-0.5 leading-none" role="img" aria-label="{{ $rating }} out of 5 stars">
    @for($i = 1; $i <= 5; $i++)
        <span class="s-star text-[14px] {{ $i <= round($rating) ? 'is-active' : '' }}">&#9733;</span>
    @endfor
</span>
