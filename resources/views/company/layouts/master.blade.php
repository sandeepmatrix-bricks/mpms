<!DOCTYPE html>
<html lang="en">
<head>
  @include('admin.partials.styles')
  <link rel="stylesheet" href="{{ asset('admin-assets/css/vendors/datatables.css') }}">
  @stack('page-styles')
</head>
<body>
  <div class="loader-wrapper">
    <div class="loader"><div class="loader4"></div></div>
  </div>
  <div class="tap-top"><i data-feather="chevrons-up"></i></div>

  <div class="page-wrapper compact-wrapper" id="pageWrapper">
    @include('company.partials.header')

    <div class="page-body-wrapper">
      @include('company.partials.sidebar')

      <div class="page-body">
        <div class="container-fluid">
          <div class="page-title">
            <div class="row">
              <div class="col-6"><h4 class="mb-0">@yield('page-title', 'Dashboard')</h4></div>
              <div class="col-6">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('company.dashboard') }}"><i data-feather="home"></i></a></li>
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

  {{-- DataTables: auto-init every table.js-datatable (search, paging, sort) --}}
  <script src="{{ asset('admin-assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script>
    jQuery(function ($) {
      if (!$.fn || !$.fn.DataTable) return;
      $('table.js-datatable').each(function () {
        if ($.fn.DataTable.isDataTable(this)) return;
        var $t = $(this), noSort = [];
        $t.find('thead th').each(function (i) {
          if ($(this).is('.no-sort') || $(this).attr('data-orderable') === 'false') noSort.push(i);
        });
        $t.DataTable({
          order: [],
          columnDefs: noSort.length ? [{ orderable: false, searchable: false, targets: noSort }] : [],
          language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries' },
        });
      });
    });
  </script>

  @include('admin.partials.sweetalert')
  @stack('page-scripts')
</body>
</html>
