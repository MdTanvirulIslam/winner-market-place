<x-admin-layout title="Edit Release">
    @vite('resources/js/editor.js')
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Releases' => route('admin.releases.index'), 'v' . $release->version => null]" />
            <h4>Edit Release</h4>
            <p class="text-[13px] text-muted">{{ $release->product->name }} — v{{ $release->version }} ({{ $release->fileSizeForHumans() }})</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.releases.update', $release) }}" enctype="multipart/form-data" class="space-y-4" data-ajax-upload>
                @csrf
                @method('PATCH')

                <div class="hidden rounded-lg border px-4 py-3 text-[13px] font-semibold" style="border-color:rgba(239,68,68,0.4);background:rgba(239,68,68,0.06);color:#dc2626;" data-upload-errors></div>

                <div>
                    <label class="panel-label" for="file">Replace Zip File (optional)</label>
                    <input class="panel-input mt-1" type="file" id="file" name="file" accept=".zip">
                    <p class="mt-1 text-[12px] text-muted">Leave empty to keep the current file.</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div>
                    <label class="panel-label" for="notes">Release Notes</label>
                    <textarea class="panel-textarea mt-1" id="notes" name="notes" rows="5" data-quill="full">{{ old('notes', $release->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="hidden space-y-1.5" data-upload-progress>
                    <div class="progress-bar-custom"><div class="progress-fill w-0"></div></div>
                    <div class="text-[12px] text-muted"><span data-upload-percent>0%</span> uploaded</div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover disabled:opacity-60">Save Changes</button>
                    <a href="{{ route('admin.releases.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
