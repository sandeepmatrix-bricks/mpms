@extends('admin.layouts.auth')

@section('title', 'Reset password')

@section('content')
  <style>.login-card { background: #ffffff !important; }</style>
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
            <form class="theme-form" method="POST" action="{{ route('company.password.update') }}">
              @csrf
              <input type="hidden" name="token" value="{{ $token }}">
              <h4>Set a new password</h4>
              <p>Choose a new password for your account.</p>

              <div class="form-group">
                <label class="col-form-label">Email Address</label>
                <input class="form-control @error('email') is-invalid @enderror" type="email"
                       name="email" value="{{ old('email', $email) }}" required autofocus>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>

              <div class="form-group">
                <label class="col-form-label">New Password</label>
                <input class="form-control @error('password') is-invalid @enderror" type="password"
                       name="password" placeholder="••••••••" required>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>

              <div class="form-group">
                <label class="col-form-label">Confirm Password</label>
                <input class="form-control" type="password" name="password_confirmation" placeholder="••••••••" required>
              </div>

              <div class="form-group mb-0">
                <button class="btn btn-primary d-block w-100" type="submit">Reset password</button>
              </div>

              <p class="mt-4 mb-0 text-center">
                <a href="{{ route('company.login') }}">← Back to sign in</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
