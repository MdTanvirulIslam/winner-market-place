@props([
    'id',
    'title',
    'message',
    'action',
    'method' => 'POST',
    'confirmLabel' => 'Confirm',
    'variant' => 'danger', // danger | accent
])

{{-- Opened by any element with data-modal-open="{{ $id }}"; requires the
     shared #modalOverlay in the admin layout. --}}
<div class="modal-panel" id="modal-{{ $id }}" role="dialog" aria-modal="true" aria-labelledby="modal-{{ $id }}-title">
    <div class="modal-header">
        <div>
            <div class="text-[11px] uppercase tracking-[0.16em] text-muted">Confirmation</div>
            <h5 id="modal-{{ $id }}-title" class="mt-2 text-lg font-bold">{{ $title }}</h5>
        </div>
        <button type="button" class="offcanvas-close" data-modal-close aria-label="Close modal"><span class="icon" data-icon="x"></span></button>
    </div>
    <div class="modal-body">
        <p class="text-[14px] leading-7 text-muted">{{ $message }}</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="rounded-lg border px-4 py-2.5 text-sm font-semibold text-text" style="border-color:var(--border);background:var(--bg-card);" data-modal-close>Cancel</button>
        <form method="POST" action="{{ $action }}">
            @csrf
            @if(strtoupper($method) !== 'POST')
                @method($method)
            @endif
            <button type="submit" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-white {{ $variant === 'danger' ? 'bg-danger' : 'bg-accent hover:bg-accent-hover' }}">{{ $confirmLabel }}</button>
        </form>
    </div>
</div>
