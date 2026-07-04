<header class="topbar" role="banner">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><span class="icon" data-icon="menu"></span></button>
        <a href="{{ route('admin.dashboard') }}" class="brand"><span class="icon" data-icon="badge-check"></span><span>{{ config('app.name') }}</span></a>
        <div class="topbar-search"><span class="icon search-icon" data-icon="search"></span><input type="text" placeholder="Search..." aria-label="Search"></div>
    </div>
    <div class="topbar-right">
        <div class="dropdown-wrap">
            <button class="topbar-btn" data-dropdown="notifications" aria-label="Notifications">
                <span class="icon" data-icon="bell"></span>
                @if($notificationCount > 0)
                    <span class="badge-count">{{ $notificationCount > 99 ? '99+' : $notificationCount }}</span>
                @endif
            </button>
            <div class="dropdown-menu-custom" id="dropdown-notifications" role="menu">
                <div class="dropdown-header-custom"><h6>Notifications</h6><a href="{{ route('admin.orders.index') }}">Orders</a></div>
                @forelse($notifications as $notification)
                    <a href="{{ $notification->url }}" class="dropdown-item-custom">
                        <div class="dropdown-item-icon" style="{{ $notification->tint }}"><span class="icon" data-icon="{{ $notification->icon }}"></span></div>
                        <div class="dropdown-item-content">
                            <p>{{ $notification->title }}</p>
                            <span>{{ $notification->time?->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="dropdown-item-custom">
                        <div class="dropdown-item-icon" style="background:rgba(13,148,136,0.1);color:#0d9488;"><span class="icon" data-icon="badge-check"></span></div>
                        <div class="dropdown-item-content"><p>All caught up</p><span>No new store events.</span></div>
                    </div>
                @endforelse
                <div class="dropdown-footer-custom"><a href="{{ route('admin.orders.index') }}">View All Orders</a></div>
            </div>
        </div>

        <button class="topbar-btn" id="darkModeToggle" aria-label="Toggle dark mode"><span class="icon" id="darkModeToggleIcon" data-icon="moon"></span></button>
        <button class="topbar-btn" id="fullscreenToggle" aria-label="Toggle fullscreen"><span class="icon" id="fullscreenToggleIcon" data-icon="maximize"></span></button>

        <div class="dropdown-wrap">
            <button class="profile-btn" data-dropdown="profile" aria-label="Profile menu">
                <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="profile-info"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Staff' }}</small></div>
            </button>
            <div class="dropdown-menu-custom profile-dropdown" id="dropdown-profile" role="menu">
                <div class="profile-summary">
                    <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="profile-summary-info"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div>
                </div>
                <a class="dropdown-item-link" href="{{ route('profile.edit') }}"><span class="icon item-icon" data-icon="user"></span> My Profile</a>
                <div class="dropdown-divider-custom"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item-link text-danger w-full"><span class="icon item-icon text-danger" data-icon="log-out"></span> Logout</button>
                </form>
            </div>
        </div>

        <button class="topbar-btn" id="settingsToggle" aria-label="Open settings panel"><span class="icon" data-icon="sliders-horizontal"></span></button>
    </div>
</header>
