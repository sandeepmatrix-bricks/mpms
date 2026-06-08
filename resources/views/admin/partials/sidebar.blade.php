@php($sprite = asset('admin-assets/svg/icon-sprite.svg'))
@php($nav = fn (string $pattern) => request()->routeIs($pattern) ? 'active' : '')
<div class="sidebar-wrapper" data-layout="stroke-svg">
  <div class="logo-wrapper">
    <a href="{{ route('admin.dashboard') }}">
      <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="MPMS">
    </a>
    <div class="back-btn"><i class="fa fa-angle-left"></i></div>
    <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"></i></div>
  </div>
  <div class="logo-icon-wrapper">
    <a href="{{ route('admin.dashboard') }}">
      <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo-icon.png') }}" alt="MPMS">
    </a>
  </div>
  <nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
    <div id="sidebar-menu">
      <ul class="sidebar-links" id="simple-bar">
        <li class="back-btn">
          <a href="{{ route('admin.dashboard') }}">
            <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo-icon.png') }}" alt="MPMS">
          </a>
          <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"></i></div>
        </li>

        <li class="sidebar-main-title"><div><h6>General</h6></div></li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-home"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-home"></use></svg>
            <span>Dashboard</span>
          </a>
        </li>

        <li class="sidebar-main-title"><div><h6>Platform</h6></div></li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.tenants.*') }}" href="{{ route('admin.tenants.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-builders"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-builders"></use></svg>
            <span>Tenants</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.users.*') }}" href="{{ route('admin.users.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-user"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-user"></use></svg>
            <span>Users</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.roles.*') }}" href="{{ route('admin.roles.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-knowledgebase"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-knowledgebase"></use></svg>
            <span>Roles</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.permissions.*') }}" href="{{ route('admin.permissions.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-file"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-file"></use></svg>
            <span>Permissions</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.memberships.*') }}" href="{{ route('admin.memberships.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-contact"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-contact"></use></svg>
            <span>Memberships</span>
          </a>
        </li>

        <li class="sidebar-main-title"><div><h6>Content</h6></div></li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.pages.*') }}" href="{{ route('admin.pages.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-sample-page"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-sample-page"></use></svg>
            <span>Pages</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.page-blocks.*') }}" href="{{ route('admin.page-blocks.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-layout"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-layout"></use></svg>
            <span>Page Blocks</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.collections.*') }}" href="{{ route('admin.collections.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-table"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-table"></use></svg>
            <span>Collections</span>
          </a>
        </li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.records.*') }}" href="{{ route('admin.records.index') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-board"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-board"></use></svg>
            <span>Records</span>
          </a>
        </li>
      </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
  </nav>
</div>
