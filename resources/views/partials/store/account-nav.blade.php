<div class="mb-8 flex flex-wrap gap-2 border-b pb-4" style="border-color:var(--border);">
    <a href="{{ route('account.orders') }}" class="rounded-full px-4 py-2 text-[13px] font-semibold {{ request()->routeIs('account.orders*') ? 'bg-accent text-white' : 'text-muted hover:text-accent' }}">My Purchases</a>
    <a href="{{ route('account.downloads') }}" class="rounded-full px-4 py-2 text-[13px] font-semibold {{ request()->routeIs('account.downloads') ? 'bg-accent text-white' : 'text-muted hover:text-accent' }}">My Downloads</a>
    <a href="{{ route('profile.edit') }}" class="rounded-full px-4 py-2 text-[13px] font-semibold text-muted hover:text-accent">Profile</a>
</div>
