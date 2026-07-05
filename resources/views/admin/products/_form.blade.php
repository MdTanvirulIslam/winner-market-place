@if($errors->any())
    <div class="rounded-lg border px-4 py-3 text-[13px] font-semibold" style="border-color:rgba(239,68,68,0.4);background:rgba(239,68,68,0.06);color:#dc2626;">
        Some changes could not be saved — check the highlighted fields below.
    </div>
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="panel-label" for="name">Name</label>
        <input class="panel-input mt-1" type="text" id="name" name="name" value="{{ old('name', $product?->name) }}" required>
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="slug">Slug</label>
        <input class="panel-input mt-1" type="text" id="slug" name="slug" value="{{ old('slug', $product?->slug) }}" placeholder="e.g. news-portal" required>
        <p class="mt-1 text-[12px] font-semibold text-warning">Must exactly match the product slug in the License Manager.</p>
        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
    </div>
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="panel-label" for="category_id">Category</label>
        <select class="panel-select mt-1" id="category_id" name="category_id">
            <option value="">— None —</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $product?->category_id) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="status">Status</label>
        <select class="panel-select mt-1" id="status" name="status" required>
            <option value="draft" @selected(old('status', $product?->status ?? 'draft') === 'draft')>Draft — hidden from the store</option>
            <option value="published" @selected(old('status', $product?->status) === 'published')>Published — visible in the store</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

<div class="grid gap-4 md:grid-cols-3">
    <div>
        <label class="panel-label" for="price">Price</label>
        <input class="panel-input mt-1" type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $product?->price) }}" required>
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="sale_price">Sale Price (optional)</label>
        <input class="panel-input mt-1" type="number" step="0.01" min="0" id="sale_price" name="sale_price" value="{{ old('sale_price', $product?->sale_price) }}">
        <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="demo_url">Demo URL (optional)</label>
        <input class="panel-input mt-1" type="url" id="demo_url" name="demo_url" value="{{ old('demo_url', $product?->demo_url) }}" placeholder="https://demo.example.com">
        <x-input-error :messages="$errors->get('demo_url')" class="mt-2" />
    </div>
</div>

<div>
    <label class="panel-label" for="short_description">Short Description</label>
    <textarea class="panel-textarea mt-1" id="short_description" name="short_description" rows="2" data-quill="minimal">{{ old('short_description', $product?->short_description) }}</textarea>
    <p class="mt-1 text-[12px] text-muted">Shown on product cards and listings (max 500 characters).</p>
    <x-input-error :messages="$errors->get('short_description')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="description">Full Description</label>
    <textarea class="panel-textarea mt-1" id="description" name="description" rows="8" data-quill="full">{{ old('description', $product?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="panel-label" for="features">Features (one per line or bullet list)</label>
        <textarea class="panel-textarea mt-1" id="features" name="features" rows="6" data-quill="list" placeholder="Responsive design&#10;Admin dashboard&#10;Multi-language">{{ old('features', $product?->features) }}</textarea>
        <x-input-error :messages="$errors->get('features')" class="mt-2" />
    </div>
    <div>
        <label class="panel-label" for="requirements">Requirements (one per line or bullet list)</label>
        <textarea class="panel-textarea mt-1" id="requirements" name="requirements" rows="6" data-quill="list" placeholder="PHP 8.2+&#10;MySQL 5.7+">{{ old('requirements', $product?->requirements) }}</textarea>
        <x-input-error :messages="$errors->get('requirements')" class="mt-2" />
    </div>
</div>

<div>
    <label class="panel-label" for="images">{{ $product ? 'Add Screenshots' : 'Screenshots' }}</label>
    <input class="panel-input mt-1" type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
    <p class="mt-1 text-[12px] text-muted">JPG, PNG, or WebP — up to 10 images, 4 MB each.</p>
    <x-input-error :messages="$errors->get('images')" class="mt-2" />
    @foreach($errors->get('images.*') as $messages)
        <x-input-error :messages="$messages" class="mt-2" />
    @endforeach
</div>
