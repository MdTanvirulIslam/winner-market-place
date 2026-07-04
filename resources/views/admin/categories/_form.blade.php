<div>
    <label class="panel-label" for="name">Name</label>
    <input class="panel-input mt-1" type="text" id="name" name="name" value="{{ old('name', $category?->name) }}" required>
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="slug">Slug</label>
    <input class="panel-input mt-1" type="text" id="slug" name="slug" value="{{ old('slug', $category?->slug) }}" placeholder="e.g. news-portals" required>
    <p class="mt-1 text-[12px] text-muted">Lowercase letters, numbers, and hyphens — used in store URLs.</p>
    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="description">Description (optional)</label>
    <textarea class="panel-textarea mt-1" id="description" name="description" rows="3">{{ old('description', $category?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
