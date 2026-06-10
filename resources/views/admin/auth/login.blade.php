@extends('admin.layouts.auth')

@section('title', 'Sign in')

@section('content')
  <style>
    /* Match the login background to the logo's background color */
    .login-card { background: #ffffff !important; }
  </style>
  <div class="row m-0">
    <div class="col-12 p-0">
      <div class="login-card login-dark">
        <div>
          <div class="text-center mb-4">
            <a class="logo" href="{{ route('admin.login') }}">
              <img class="img-fluid for-light" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="MPMS">
              <img class="img-fluid for-dark" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="MPMS">
            </a>
          </div>
          <div class="login-main">
            <form class="theme-form" method="POST" action="{{ route('admin.login.attempt') }}">
              @csrf
              <h4>Sign in to MPMS</h4>
              <p>Platform administrator console</p>

              @if (session('status'))
                <div class="alert alert-success py-2">{{ session('status') }}</div>
              @endif

              <div class="form-group">
                <label class="col-form-label">Email Address</label>
                <input class="form-control @error('email') is-invalid @enderror" type="email"
                       name="email" value="{{ old('email') }}" placeholder="priya@mpms.local" required autofocus>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label class="col-form-label">Password</label>
                <input class="form-control" type="password" name="password" placeholder="••••••••" required>
              </div>

              <div class="form-group mb-0">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="checkbox p-0">
                    <input id="remember" type="checkbox" name="remember">
                    <label class="text-muted" for="remember">Remember me</label>
                  </div>
                  <a class="link" href="{{ route('admin.password.request') }}">Forgot password?</a>
                </div>
                <button class="btn btn-primary d-block w-100 mt-3" type="submit">Sign in</button>
              </div>

              <p class="mt-4 mb-0 text-center text-muted">
                Seeded admin: <code>priya@mpms.local</code> / <code>password</code>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
