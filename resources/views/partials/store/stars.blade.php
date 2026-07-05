{{-- $rating: 0-5 (float ok). Filled stars in amber, the rest muted. --}}
<span class="inline-flex items-center gap-0.5 leading-none" role="img" aria-label="{{ $rating }} out of 5 stars">
    @for($i = 1; $i <= 5; $i++)
        <span class="text-[14px]" style="color:{{ $i <= round($rating) ? '#f59e0b' : 'var(--border)' }};">&#9733;</span>
    @endfor
</span>
