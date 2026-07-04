<div>
    <label class="panel-label" for="name">Name</label>
    <input class="panel-input mt-1" type="text" id="name" name="name" value="{{ old('name', $user?->name) }}" required>
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="email">Email</label>
    <input class="panel-input mt-1" type="email" id="email" name="email" value="{{ old('email', $user?->email) }}" required>
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="role">Role</label>
    <select class="panel-select mt-1" id="role" name="role" required>
        <option value="staff" @selected(old('role', $user?->role) === 'staff')>Staff — manage catalog and orders</option>
        <option value="super_admin" @selected(old('role', $user?->role) === 'super_admin')>Super Admin — everything, incl. settings and users</option>
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="password">{{ $user ? 'New Password (leave blank to keep current)' : 'Password' }}</label>
    <input class="panel-input mt-1" type="password" id="password" name="password" @if(!$user) required @endif autocomplete="new-password">
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <label class="panel-label" for="password_confirmation">Confirm Password</label>
    <input class="panel-input mt-1" type="password" id="password_confirmation" name="password_confirmation" @if(!$user) required @endif autocomplete="new-password">
</div>
