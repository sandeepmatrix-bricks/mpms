<!DOCTYPE html>
<html lang="en">
<head>
  @include('admin.partials.styles')
</head>
<body>
  {{-- loader --}}
  <div class="loader-wrapper">
    <div class="loader"><div class="loader4"></div></div>
  </div>
  {{-- tap on top --}}
  <div class="tap-top"><i data-feather="chevrons-up"></i></div>

  <div class="page-wrapper compact-wrapper" id="pageWrapper">
    @include('admin.partials.header')

    <div class="page-body-wrapper">
      @include('admin.partials.sidebar')

      <div class="page-body">
        <div class="container-fluid">
          <div class="page-title">
            <div class="row">
              <div class="col-6">
                <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
              </div>
              <div class="col-6">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}"><i data-feather="home"></i></a>
                  </li>
                  @yield('breadcrumbs')
                </ol>
              </div>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          @yield('content')
        </div>
      </div>

      @include('admin.partials.footer')
    </div>
  </div>

  @include('admin.partials.scripts')
  @include('admin.partials.sweetalert')
</body>
</html>
