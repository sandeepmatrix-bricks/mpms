@extends('admin.layouts.master')

@section('title', 'Reset Password')
@section('page-title', 'Reset Password')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
  <li class="breadcrumb-item active">Reset password</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Reset password — {{ $company->name }}</h4>
          <small class="text-muted">Set a new password for <strong>{{ $admin->email }}</strong>. No email is sent — share it directly.</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.companies.reset-password.update', $company) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
              <label class="form-label">New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="text" name="password" id="pwd"
                       class="form-control @error('password') is-invalid @enderror"
                       value="{{ old('password', $suggestion) }}" required minlength="8">
                <button type="button" class="btn btn-outline-secondary" onclick="regen()" title="Generate a new one">
                  <i data-feather="refresh-cw" style="width:15px;"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <small class="text-muted">Minimum 8 characters. You can type your own or keep the suggested one.</small>
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Update Password</button>
              <a href="{{ route('admin.companies.index') }}" class="btn btn-light">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function regen() {
      const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
      let p = '';
      for (let i = 0; i < 12; i++) p += chars[Math.floor(Math.random() * chars.length)];
      document.getElementById('pwd').value = p;
    }
  </script>
@endsection
