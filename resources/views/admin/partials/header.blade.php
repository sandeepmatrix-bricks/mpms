<div class="page-header">
  <div class="header-wrapper row m-0">
    <div class="header-logo-wrapper col-auto p-0">
      <div class="logo-wrapper">
        <a href="{{ route('admin.dashboard') }}">
          <img class="img-fluid for-light" src="{{ asset('admin-assets/images/logo/logo_dark.png') }}" alt="MPMS">
          <img class="img-fluid for-dark" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="MPMS">
        </a>
      </div>
      <div class="toggle-sidebar">
        <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
      </div>
    </div>

    <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
      <div class="d-flex align-items-center gap-2">
        <h4 class="f-w-600 mb-0">Platform Console</h4>
      </div>
      <div class="welcome-content d-xl-block d-none">
        <span class="text-truncate col-12">Overseeing every company on MPMS.</span>
      </div>
    </div>

    <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
      <ul class="nav-menus">
        <li class="profile-nav onhover-dropdown p-0">
          <div class="d-flex profile-media align-items-center">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                 style="width:36px;height:36px;font-weight:600;">
              {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1 ms-2">
              <span>{{ auth()->user()->name }}</span>
              <p class="mb-0 font-roboto">{{ auth()->user()->adminRoleLabel() }} <i class="middle fa fa-angle-down"></i></p>
            </div>
          </div>
          <ul class="profile-dropdown onhover-show-div">
            <li><span class="text-muted small px-2">{{ auth()->user()->email }}</span></li>
            <li>
              <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="dropdown-item border-0 bg-transparent p-0 d-flex align-items-center gap-2">
                  <i data-feather="log-out"></i><span>Log out</span>
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
