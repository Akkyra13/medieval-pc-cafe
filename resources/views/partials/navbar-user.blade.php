<header class="topbar">
    <div class="brand">NEXUSNET BILLING</div>
    <nav class="nav-group">
        <a href="{{ route('user.denah') }}" class="nav-link-btn {{ request()->routeIs('user.denah') ? 'active' : '' }}">Denah PC</a>
        <a href="{{ route('user.billing') }}" class="nav-link-btn {{ request()->routeIs('user.billing') ? 'active' : '' }}">Beli Billing</a>
        <a href="{{ route('user.profile') }}" class="nav-link-btn {{ request()->routeIs('user.profile') ? 'active' : '' }}">Profile</a>
    </nav>
    <div class="user-meta">
        <span id="session-info"></span>
        <button type="button" id="logout-btn" class="btn btn-secondary btn-sm">Keluar</button>
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
        </form>
    </div>
</header>