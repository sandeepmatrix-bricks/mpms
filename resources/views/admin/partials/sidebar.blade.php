@php($sprite = asset('admin-assets/svg/icon-sprite.svg'))
@php($nav = fn (string $pattern) => request()->routeIs($pattern) ? 'active' : '')
<div class="sidebar-wrapper" data-layout="stroke-svg">
  <div class="logo-wrapper">
    <a href="{{ route('admin.dashboard') }}">
      <img class="img-fluid" style="background: white;" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="MPMS">
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

        <li class="sidebar-main-title"><div><h6>{{ auth()->user()->adminRoleLabel() }}</h6></div></li>
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-home"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-home"></use></svg>
            <span>Dashboard</span>
          </a>
        </li>

        @permission('companies.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.companies.*') }}" href="{{ route('admin.companies.index') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-builders"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#fill-builders"></use></svg>
              <span>Companies</span>
            </a>
          </li>
        @endpermission

        @permission('users.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.users.*') }}" href="{{ route('admin.users.index') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-user"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#fill-user"></use></svg>
              <span>Users</span>
            </a>
          </li>
        @endpermission

        @permission('sub_admins.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.sub-admins.*') }}" href="{{ route('admin.sub-admins.index') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-contact"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#fill-contact"></use></svg>
              <span>Sub Admins</span>
            </a>
          </li>
        @endpermission

        @permission('roles.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('admin.roles.*') }}" href="{{ route('admin.roles.index') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-knowledgebase"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#fill-knowledgebase"></use></svg>
              <span>Roles</span>
            </a>
          </li>
        @endpermission
      </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
  </nav>
</div>
