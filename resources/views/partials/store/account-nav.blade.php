<div class="s-card mb-8 flex flex-wrap gap-1.5 p-2">
    <a href="{{ route('account.orders') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-[13px] font-semibold transition-colors duration-300 {{ request()->routeIs('account.orders*') ? 'bg-accent text-white' : 'text-muted hover:bg-accent-subtle hover:text-accent-light' }}">
        <span class="icon" data-icon="shopping-cart"></span> My Purchases
    </a>
    <a href="{{ route('account.downloads') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-[13px] font-semibold transition-colors duration-300 {{ request()->routeIs('account.downloads') ? 'bg-accent text-white' : 'text-muted hover:bg-accent-subtle hover:text-accent-light' }}">
        <span class="icon" data-icon="download"></span> My Downloads
    </a>
    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-[13px] font-semibold transition-colors duration-300 {{ request()->routeIs('profile.edit') ? 'bg-accent text-white' : 'text-muted hover:bg-accent-subtle hover:text-accent-light' }}">
        <span class="icon" data-icon="user"></span> Profile
    </a>
</div>
