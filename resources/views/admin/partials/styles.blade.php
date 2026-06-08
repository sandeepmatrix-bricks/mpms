<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="MPMS — Multi-tenant Portal Management System">
<meta name="author" content="MPMS">
<link rel="icon" href="{{ asset('admin-assets/images/favicon.png') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('admin-assets/images/favicon.png') }}" type="image/x-icon">
<title>@yield('title', 'Dashboard') · MPMS</title>

{{-- Google font --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Icon fonts --}}
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/icofont.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/themify.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/flag-icon.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/feather-icon.css') }}">

{{-- Plugins --}}
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/scrollbar.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/animate.css') }}">

{{-- Bootstrap + app theme --}}
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/vendors/bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/style.css') }}">
<link id="color" rel="stylesheet" href="{{ asset('admin-assets/css/color-1.css') }}" media="screen">
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/responsive.css') }}">

@stack('page-styles')
