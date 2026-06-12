@php($sprite = asset('admin-assets/svg/icon-sprite.svg'))
@php($nav = fn (string $pattern) => request()->routeIs($pattern) ? 'active' : '')
<div class="sidebar-wrapper" data-layout="stroke-svg">
  <div class="logo-wrapper">
    <a href="{{ route('company.dashboard') }}">
      <img class="img-fluid d-block mx-auto" style="background: white;" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="{{ $company->name }}" style="max-height:46px;">
    </a>
    <div class="back-btn"><i class="fa fa-angle-left"></i></div>
    <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"></i></div>
  </div>
  <div class="logo-icon-wrapper">
    <a href="{{ route('company.dashboard') }}">
      <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo-icon.png') }}" alt="{{ $company->name }}">
    </a>
  </div>
  <nav class="sidebar-main">
    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
    <div id="sidebar-menu">
      <ul class="sidebar-links" id="simple-bar">
        <li class="back-btn">
          <a href="{{ route('company.dashboard') }}">
            <img class="img-fluid" src="{{ asset('admin-assets/images/logo/logo-icon.png') }}" alt="{{ $company->name }}">
          </a>
          <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"></i></div>
        </li>

        <li class="sidebar-main-title"><div><h6>Menu</h6></div></li>

        {{-- Dashboards: always visible --}}
        <li class="sidebar-list">
          <a class="sidebar-link sidebar-title link-nav {{ $nav('company.dashboard') }}" href="{{ route('company.dashboard') }}">
            <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-home"></use></svg>
            <svg class="fill-icon"><use href="{{ $sprite }}#fill-home"></use></svg>
            <span>Dashboards</span>
          </a>
        </li>

        {{-- User Management: Add Role + Add User (collapsible) --}}
        @if (auth()->user()->hasPermission('users.read') || auth()->user()->hasPermission('roles.read'))
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ $nav('company.users.*') ?: $nav('company.roles.*') }}" href="javascript:void(0)">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-user"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#fill-user"></use></svg>
              <span>User Management</span>
            </a>
            <ul class="sidebar-submenu">
              @permission('roles.read')
                <li><a class="{{ $nav('company.roles.*') }}" href="{{ route('company.roles.index') }}">Add Role</a></li>
              @endpermission
              @permission('users.read')
                <li><a class="{{ $nav('company.users.*') }}" href="{{ route('company.users.index') }}">Add User</a></li>
                <li><a class="{{ $nav('company.activity-logs.*') }}" href="{{ route('company.activity-logs.index') }}">Activity Logs</a></li>
              @endpermission
            </ul>
          </li>
        @endif

        {{-- Job Management (Departments live now; Designation/Job Descriptions in a later slice) --}}
        @if (auth()->user()->hasPermission('job_categories.read') || auth()->user()->hasPermission('jobs.read'))
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ $nav('company.career-page.*') ?: ($nav('company.job-categories.*') ?: ($nav('company.job-listings.*') ?: $nav('company.job-descriptions.*'))) }}" href="javascript:void(0)">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-blog"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#stroke-blog"></use></svg>
              <span>Job Management</span>
            </a>
            <ul class="sidebar-submenu">
              @permission('jobs.read')
                <li><a class="{{ $nav('company.career-page.*') }}" href="{{ route('company.career-page.edit') }}">Main Page</a></li>
              @endpermission
              @permission('job_categories.read')
                <li><a class="{{ $nav('company.job-categories.*') }}" href="{{ route('company.job-categories.index') }}">Departments</a></li>
              @endpermission
              @permission('jobs.read')
                <li><a class="{{ $nav('company.job-listings.*') }}" href="{{ route('company.job-listings.index') }}">Designation</a></li>
                <li><a class="{{ $nav('company.job-descriptions.*') }}" href="{{ route('company.job-descriptions.index') }}">Job Descriptions</a></li>
              @endpermission
            </ul>
          </li>
        @endif

        {{-- Job Applicant: completed applications --}}
        @permission('applicants.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('company.applicants.index') || request()->routeIs('company.applicants.show') ? 'active' : '' }}" href="{{ route('company.applicants.index') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-board"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#stroke-board"></use></svg>
              <span>Job Applicant</span>
            </a>
          </li>

          {{-- Incomplete Record: unfinished drafts --}}
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('company.applicants.incomplete') }}" href="{{ route('company.applicants.incomplete') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-board"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#stroke-board"></use></svg>
              <span>Incomplete Record</span>
            </a>
          </li>
        @endpermission

        @permission('statuses.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('company.statuses.*') }}" href="{{ route('company.statuses.index') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-to-do"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#stroke-to-do"></use></svg>
              <span>Application Statuses</span>
            </a>
          </li>
        @endpermission

        @permission('settings.read')
          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title link-nav {{ $nav('company.settings.*') }}" href="{{ route('company.settings.edit') }}">
              <svg class="stroke-icon"><use href="{{ $sprite }}#stroke-builders"></use></svg>
              <svg class="fill-icon"><use href="{{ $sprite }}#stroke-builders"></use></svg>
              <span>Settings</span>
            </a>
          </li>
        @endpermission
      </ul>
    </div>
    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
  </nav>
</div>
