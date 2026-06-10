<!DOCTYPE html>
<html lang="en">
<head>
  @include('admin.partials.styles')
  <style>
    /* Keep the brand logo compact and tidy on every auth screen. */
    .login-card .logo { display:inline-block; margin-bottom:8px; }
    .login-card .logo img { max-height:54px; width:auto; }
  </style>
</head>
<body>
  <div class="container-fluid p-0">
    @yield('content')
  </div>

  {{-- Minimal scripts for the auth screen (no sidebar JS needed here) --}}
  <script src="{{ asset('admin-assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('admin-assets/js/bootstrap/popper.min.js') }}"></script>
  <script src="{{ asset('admin-assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('admin-assets/js/icons/feather-icon/feather.min.js') }}"></script>
  <script>document.addEventListener('DOMContentLoaded', () => window.feather && feather.replace());</script>
  @include('admin.partials.sweetalert')
</body>
</html>
