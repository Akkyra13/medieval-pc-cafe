<header class="topbar">
  <div class="brand">NEXUSNET BILLING</div>
  <nav class="nav-group">
    <a href="{{ route('admin.pc') }}" class="nav-link-btn {{ request()->routeIs('admin.pc') ? 'active' : '' }}">Manajemen PC</a>
    <a href="{{ route('admin.akun') }}" class="nav-link-btn {{ request()->routeIs('admin.akun') ? 'active' : '' }}">Manajemen Akun</a>
    <a href="{{ route('admin.billing') }}" class="nav-link-btn {{ request()->routeIs('admin.billing') ? 'active' : '' }}">Manajemen Billing</a>
  </nav>
  <div class="user-meta">
    <span id="session-info"></span>
    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Keluar</a>
  </div>
</header>