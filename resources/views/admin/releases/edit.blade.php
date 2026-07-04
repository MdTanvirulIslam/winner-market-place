<x-admin-layout title="Edit Release">
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Releases' => route('admin.releases.index'), 'v' . $release->version => null]" />
            <h4>Edit Release</h4>
            <p class="text-[13px] text-muted">{{ $release->product->name }} — v{{ $release->version }} ({{ $release->fileSizeForHumans() }})</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.releases.update', $release) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="panel-label" for="file">Replace Zip File (optional)</label>
                    <input class="panel-input mt-1" type="file" id="file" name="file" accept=".zip">
                    <p class="mt-1 text-[12px] text-muted">Leave empty to keep the current file.</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div>
                    <label class="panel-label" for="notes">Release Notes</label>
                    <textarea class="panel-textarea mt-1" id="notes" name="notes" rows="5">{{ old('notes', $release->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Save Changes</button>
                    <a href="{{ route('admin.releases.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
