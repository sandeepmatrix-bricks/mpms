<!DOCTYPE html>
<html lang="en">
<head>
  @include('admin.partials.styles')
  {{-- DataTables (tenant portal lists) --}}
  <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/datatables.css') }}">
  @stack('tenant-styles')
</head>
<body>
  <div class="loader-wrapper">
    <div class="loader"><div class="loader4"></div></div>
  </div>
  <div class="tap-top"><i data-feather="chevrons-up"></i></div>

  <div class="page-wrapper compact-wrapper" id="pageWrapper">
    @include('tenant.partials.header')

    <div class="page-body-wrapper">
      @include('tenant.partials.sidebar')

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
                    <a href="{{ route('tenant.dashboard', $tenant) }}"><i data-feather="home"></i></a>
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

  {{-- DataTables: any <table class="datatable"> becomes searchable/sortable/paginated --}}
  <script src="{{ asset('admin-assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('admin-assets/js/datatable/datatable-extension/dataTables.bootstrap5.min.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery && jQuery.fn.DataTable) {
        jQuery('table.datatable').DataTable({
          order: [],
          columnDefs: [{ orderable: false, targets: 'no-sort' }],
          language: { search: '', searchPlaceholder: 'Search…' },
        });
      }
      if (window.feather) feather.replace();
    });
  </script>
  @stack('tenant-scripts')
</body>
</html>
