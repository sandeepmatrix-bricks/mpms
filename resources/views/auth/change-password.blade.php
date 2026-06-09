@extends('admin.layouts.auth')

@section('content')
  <section>
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-5 mx-auto p-0">
          <div class="login-card login-dark" style="min-height:100vh; display:flex; align-items:center; justify-content:center;">
            <div style="width:100%; max-width:420px;">
              <div class="text-center mb-4">
                <img class="img-fluid" style="max-height:48px;" src="{{ asset('admin-assets/images/logo/logo.png') }}" alt="logo">
              </div>
              <div class="login-main">
                <form class="theme-form" method="POST" action="{{ route('password.change.update') }}">
                  @csrf
                  <h4>Set your password</h4>
                  <p>For your security, please choose a new password before continuing.</p>

                  <div class="form-group mb-3">
                    <label class="col-form-label">New Password</label>
                    <input class="form-control @error('password') is-invalid @enderror" type="password"
                           name="password" required minlength="8" placeholder="Minimum 8 characters">
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>

                  <div class="form-group mb-3">
                    <label class="col-form-label">Confirm Password</label>
                    <input class="form-control" type="password" name="password_confirmation" required
                           placeholder="Re-enter the password">
                  </div>

                  <div class="form-group mb-0">
                    <button class="btn btn-primary w-100" type="submit">Save password &amp; continue</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
