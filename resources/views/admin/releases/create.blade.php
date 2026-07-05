<x-admin-layout title="Upload Release">
    @vite('resources/js/editor.js')
    <div class="page-header animate-in opacity-0">
        <div>
            <x-breadcrumb :items="['Releases' => route('admin.releases.index'), 'Upload' => null]" />
            <h4>Upload Release</h4>
            <p class="text-[13px] text-muted">A new downloadable version of a product.</p>
        </div>
    </div>

    <div class="content-card animate-in opacity-0 max-w-2xl">
        <div class="content-card-body">
            <form method="POST" action="{{ route('admin.releases.store') }}" enctype="multipart/form-data" class="space-y-4" data-ajax-upload>
                @csrf

                <div class="hidden rounded-lg border px-4 py-3 text-[13px] font-semibold" style="border-color:rgba(239,68,68,0.4);background:rgba(239,68,68,0.06);color:#dc2626;" data-upload-errors></div>
                <div>
                    <label class="panel-label" for="product_id">Product</label>
                    <select class="panel-select mt-1" id="product_id" name="product_id" required>
                        <option value="">— Choose product —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected((int) old('product_id', $preselected) === $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div>
                    <label class="panel-label" for="version">Version</label>
                    <input class="panel-input mt-1" type="text" id="version" name="version" value="{{ old('version') }}" placeholder="e.g. 1.0.0" required>
                    <x-input-error :messages="$errors->get('version')" class="mt-2" />
                </div>

                <div>
                    <label class="panel-label" for="file">Zip File</label>
                    <input class="panel-input mt-1" type="file" id="file" name="file" accept=".zip" required>
                    <p class="mt-1 text-[12px] text-muted">The application zip customers will download. Stored privately on the server.</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div>
                    <label class="panel-label" for="notes">Release Notes (shown in the public changelog)</label>
                    <textarea class="panel-textarea mt-1" id="notes" name="notes" rows="5" data-quill="full" placeholder="What changed in this version?">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <label class="flex items-center gap-2 text-sm text-text">
                    <input type="checkbox" name="notify_buyers" value="1" @checked(old('notify_buyers', true))>
                    Email buyers about this update
                </label>

                <div class="hidden space-y-1.5" data-upload-progress>
                    <div class="progress-bar-custom"><div class="progress-fill w-0"></div></div>
                    <div class="text-[12px] text-muted"><span data-upload-percent>0%</span> uploaded</div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover disabled:opacity-60">Upload Release</button>
                    <a href="{{ route('admin.releases.index') }}" class="text-sm font-semibold text-muted">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
