@php
    $currentUser = auth()->user();
    $userName = $currentUser ? $currentUser->name : 'User';
    $userRole = $currentUser ? $currentUser->role : 'Role';
@endphp
<header class="top-nav">
    <div class="top-nav-left">
        <button class="nav-btn" id="sidebarToggle" type="button"><i data-lucide="menu"
                style="width: 1.25rem; height: 1.25rem;"></i></button>
        <nav aria-label="breadcrumb" class="breadcrumb-wrapper">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ View::hasSection('title') ? View::getSection('title') : 'Home' }}</li>
            </ol>
        </nav>
    </div>
    <div class="top-nav-right">
        <button class="search-btn d-none d-md-flex" id="searchToggle" type="button"><i data-lucide="search"
                style="width: 1rem; height: 1rem;"></i><span>Search...</span><kbd>Ctrl K</kbd></button>
        <button class="nav-btn d-md-none" id="searchToggleMobile" type="button"><i data-lucide="search"
                style="width: 1.25rem; height: 1.25rem;"></i></button>
        <button class="nav-btn" id="themeToggle" type="button"><i data-lucide="sun" id="themeIconLight"
                style="width: 1.25rem; height: 1.25rem;"></i><i data-lucide="moon" id="themeIconDark" class="d-none"
                style="width: 1.25rem; height: 1.25rem;"></i></button>
        <div class="nav-btn-wrapper">
            <button class="nav-btn" id="notificationToggle" type="button"><i data-lucide="bell"
                    style="width: 1.25rem; height: 1.25rem;"></i><span class="badge badge-danger"
                    id="notificationBadge">0</span></button>
        </div>
        <div class="dropdown">
            <button class="top-nav-user" data-bs-toggle="dropdown" type="button">
                <div class="avatar avatar-sm">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=2563eb&color=fff" alt="{{ $userName }}">
                </div>
                <div class="top-nav-user-info">
                    <div class="top-nav-user-name">{{ $userName }}</div>
                    <div class="top-nav-user-role">{{ $userRole }}</div>
                </div>
                <i data-lucide="chevron-down" style="width: 1rem; height: 1rem; color: var(--text-tertiary);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <a class="dropdown-item" href="{{ route('user-profile.index') }}">
                        <i data-lucide="user" style="width: 1rem; height: 1rem;" class="me-1.5"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('password.edit') }}">
                        <i data-lucide="shield" style="width: 1rem; height: 1rem;" class="me-1.5"></i> Security & Password
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger fw-semibold" type="submit">
                            <i data-lucide="log-out" style="width: 1rem; height: 1rem;" class="me-1.5"></i> Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
