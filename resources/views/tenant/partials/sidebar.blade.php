@php($sprite = asset('admin-assets/svg/icon-sprite.svg'))
@php($nav = fn (...$patterns) => request()->routeIs(...$patterns) ? 'active' : '')
@php($contentActive = request()->routeIs('tenant.pages.*', 'tenant.page-blocks.*', 'tenant.collections.*', 'tenant.records.*'))
<div class="sidebar-wrapper" data-layout="stroke-svg">
  <div class="logo-wrapper">
    <a href="{{ route('tenant.dashboard', $tenant) }}">
      <img class="img-fluid d-block mx-auto for-light" src="{{ asset('admin-assets/images/logo/logo_dark.png') }}" alt="{{ $tenant->name }}" style="max-height:46px;">
      <img class="img-fluid d-block mx-auto for-dark" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="{{ $tenant->name }}" style="max-height:46px;">
    </a>
    <div class="back-btn"><i class="fa fa-angle-left"></i></div>
    <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"></i></div>
  </div>
  <div class="logo-icon-wrapper">
    <a href="{{ route('tenant.dashboard', $tenant) }}">
      <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo-icon.png') }}" alt="{{ $tenant->name }}">
    </a>
  </div>
  <nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
    <div id="sidebar-menu">
      <ul class="sidebar-links" id="simple-bar">
        <li class="back-btn">
          <a href="{{ route('tenant.dashboard', $tenant) }}">
            <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo-icon.png') }}" alt="{{ $tenant->name }}">
          </a>
          <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"></i></div>
        </li>

        <li class="sidebar-main-title"><div><h6>General</h6></div></li>
        <li class="sidebar-list">
          <i class="fa fa-thumb-tack"></i>
          <a class="sidebar-link sidebar-title link-nav {{ $nav('tenant.dashboard') }}" href="{{ route('tenant.dashboard', $tenant) }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-home"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-home"></use></svg>
            <span>Dashboard</span>
          </a>
        </li>

        @if (auth()->user()?->hasPermission('manage_users') || auth()->user()?->hasPermission('manage_roles'))
          <li class="sidebar-main-title"><div><h6>Company</h6></div></li>
          @permission('manage_users')
            <li class="sidebar-list">
              <i class="fa fa-thumb-tack"></i>
              <a class="sidebar-link sidebar-title link-nav {{ $nav('tenant.users.*') }}" href="{{ route('tenant.users.index', $tenant) }}">
                <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-user"></use></svg>
                <svg class="fill-icon"><use href="{{ $sprite }}#fill-user"></use></svg>
                <span>Team</span>
              </a>
            </li>
          @endpermission
          @permission('manage_roles')
            <li class="sidebar-list">
              <i class="fa fa-thumb-tack"></i>
              <a class="sidebar-link sidebar-title link-nav {{ $nav('tenant.roles.*') }}" href="{{ route('tenant.roles.index', $tenant) }}">
                <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-knowledgebase"></use></svg>
                <svg class="fill-icon"><use href="{{ $sprite }}#fill-knowledgebase"></use></svg>
                <span>Roles</span>
              </a>
            </li>
          @endpermission
        @endif

        @permission('manage_pages')
          <li class="sidebar-main-title"><div><h6>Content</h6></div></li>
          <li class="sidebar-list">
            <i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title {{ $contentActive ? 'active' : '' }}" href="javascript:void(0)">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-layout"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#fill-layout"></use></svg>
              <span>Site Builder</span>
            </a>
            <ul class="sidebar-submenu" style="display: {{ $contentActive ? 'block' : 'none' }};">
              <li><a class="{{ $nav('tenant.pages.*') }}" href="{{ route('tenant.pages.index', $tenant) }}">Pages</a></li>
              <li><a class="{{ $nav('tenant.page-blocks.*') }}" href="{{ route('tenant.page-blocks.index', $tenant) }}">Page Sections</a></li>
              <li><a class="{{ $nav('tenant.collections.*') }}" href="{{ route('tenant.collections.index', $tenant) }}">Collections</a></li>
              <li><a class="{{ $nav('tenant.records.*') }}" href="{{ route('tenant.records.index', $tenant) }}">Records</a></li>
            </ul>
          </li>
        @endpermission
      </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
  </nav>
</div>
