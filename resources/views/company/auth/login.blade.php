@extends('admin.layouts.auth')

@section('title', 'Company Sign in')

@section('content')
  <div class="row m-0">
    <div class="col-12 p-0">
      <div class="login-card login-dark">
        <div>
          <div class="text-center mb-4">
            <a class="logo" href="{{ route('company.login') }}">
              <img class="img-fluid for-light" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="logo">
              <img class="img-fluid for-dark" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="logo">
            </a>
          </div>
          <div class="login-main">
            <form class="theme-form" method="POST" action="{{ route('company.login.attempt') }}">
              @csrf
              <h4>Company Sign in</h4>
              <p>Access your company workspace</p>

              <div class="form-group">
                <label class="col-form-label">Email Address</label>
                <input class="form-control @error('email') is-invalid @enderror" type="email"
                       name="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>

              <div class="form-group">
                <label class="col-form-label">Password</label>
                <input class="form-control" type="password" name="password" placeholder="••••••••" required>
              </div>

              <div class="form-group mb-0">
                <div class="checkbox p-0">
                  <input id="remember" type="checkbox" name="remember">
                  <label class="text-muted" for="remember">Remember me</label>
                </div>
                <button class="btn btn-primary d-block w-100 mt-3" type="submit">Sign in</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
