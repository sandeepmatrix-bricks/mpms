@extends('admin.layouts.auth')

@section('title', 'Forgot password')

@section('content')
  <style>.login-card { background: #ffffff !important; }</style>
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
            <form class="theme-form" method="POST" action="{{ route('admin.password.email') }}">
              @csrf
              <h4>Forgot your password?</h4>
              <p>Enter your email and we'll send you a reset link.</p>

              @if (session('status'))
                <div class="alert alert-success py-2">{{ session('status') }}</div>
              @endif

              <div class="form-group">
                <label class="col-form-label">Email Address</label>
                <input class="form-control @error('email') is-invalid @enderror" type="email"
                       name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>

              <div class="form-group mb-0">
                <button class="btn btn-primary d-block w-100" type="submit">Send reset link</button>
              </div>

              <p class="mt-4 mb-0 text-center">
                <a href="{{ route('admin.login') }}">← Back to sign in</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
