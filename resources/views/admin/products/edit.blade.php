<x-admin-layout title="Edit Product">
    @vite('resources/js/editor.js')
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Products' => route('admin.products.index'), $product->name => null]" />
            <h4>Edit Product</h4>
            <p class="text-[13px] text-muted">{{ $product->name }} — <code>{{ $product->slug }}</code></p>
        </div>
        <a href="{{ route('admin.releases.create', ['product_id' => $product->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">
            <span class="icon" data-icon="download"></span> Upload Release
        </a>
    </div>

    <div class="grid gap-3 xl:grid-cols-12">
        <div class="animate-in opacity-0 xl:col-span-8">
            <div class="content-card">
                <div class="content-card-header"><h5>Details</h5></div>
                <div class="content-card-body">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        @include('admin.products._form')
                        <div class="flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Save Changes</button>
                            <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-muted">Back to list</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="animate-in opacity-0 xl:col-span-4 space-y-3">
            <div class="content-card">
                <div class="content-card-header"><h5>Screenshots ({{ $product->images->count() }})</h5></div>
                <div class="content-card-body">
                    @forelse($product->images as $image)
                        <div class="mb-3 flex items-center gap-3 border-b pb-3 last:border-b-0" style="border-color:var(--border);">
                            <img src="{{ $image->url() }}" alt="Screenshot" class="h-14 w-24 rounded-sm object-cover">
                            <span class="flex-1 text-[12px] text-muted">#{{ $image->sort_order }}</span>
                            <div x-data="{ open: false }">
                                <button type="button" @click="open = true" class="text-[13px] font-semibold text-danger">Remove</button>

                                {{-- Self-contained Alpine confirm — intentionally not using the
                                     shared AppModals helper, which silently fails in production.
                                     Teleported to <body>: the .animate-in ancestor retains a
                                     transform, which would trap position:fixed inside the card. --}}
                                <template x-teleport="body">
                                <div x-show="open" x-transition.opacity.duration.200ms style="display:none" @click.self="open = false" @keydown.escape.window="open = false" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4">
                                    <div class="w-full max-w-sm rounded-xl border p-6 shadow-2xl" style="background:var(--bg-card);border-color:var(--border);" @click.stop>
                                        <div class="mb-4 flex items-start gap-3">
                                            <span class="icon mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-danger" style="background:rgba(239,68,68,0.12);" data-icon="triangle-alert"></span>
                                            <div>
                                                <h5 class="text-[15px] font-bold text-text">Remove this screenshot?</h5>
                                                <p class="mt-1 text-[13px] leading-6 text-muted">The image file will be permanently deleted from the server. This cannot be undone.</p>
                                            </div>
                                        </div>
                                        <img src="{{ $image->url() }}" alt="Screenshot to remove" class="mb-4 aspect-video w-full rounded-lg border object-cover" style="border-color:var(--border);">
                                        <div class="flex justify-end gap-2.5">
                                            <button type="button" @click="open = false" class="rounded-lg border px-4 py-2 text-[13px] font-semibold text-text" style="border-color:var(--border);">Cancel</button>
                                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-danger px-4 py-2 text-[13px] font-semibold text-white transition-opacity duration-200 hover:opacity-90">Remove Screenshot</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </template>
                            </div>
                        </div>
                    @empty
                        <p class="text-[13px] text-muted">No screenshots yet — add some in the form.</p>
                    @endforelse
                </div>
            </div>

            <div class="content-card">
                <div class="content-card-header"><h5>Releases ({{ $product->releases->count() }})</h5></div>
                <div class="content-card-body">
                    @forelse($product->releases as $release)
                        <div class="mb-3 flex items-center justify-between border-b pb-3 last:border-b-0" style="border-color:var(--border);">
                            <div>
                                <div class="text-sm font-semibold">v{{ $release->version }}</div>
                                <div class="text-[12px] text-muted">{{ $release->released_at?->format('d M Y') }} · {{ $release->fileSizeForHumans() }}</div>
                            </div>
                            <a href="{{ route('admin.releases.edit', $release) }}" class="text-[13px] font-semibold text-accent">Edit</a>
                        </div>
                    @empty
                        <p class="text-[13px] text-muted">No releases uploaded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</x-admin-layout>
