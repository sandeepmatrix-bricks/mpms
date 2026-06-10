@extends('admin.layouts.master')

@section('title', 'Set up Admin')
@section('page-title', 'Set up Company Admin')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
  <li class="breadcrumb-item active">Set up admin</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Step 2 of 2 — Admin account for {{ $company->name }}</h4>
          <small class="text-muted">This person logs in at <code>/company/login</code> and manages the company.</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.companies.setup.store', $company) }}">
            @csrf
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" value="{{ old('admin_name') }}" required autofocus>
                @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" value="{{ old('admin_email', $company->email) }}" required>
                @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="form-check form-switch mb-3">
              <input type="hidden" name="auto_password" value="0">
              <input class="form-check-input" type="checkbox" role="switch" id="auto_password" name="auto_password" value="1"
                     @checked(old('auto_password', true)) onchange="document.getElementById('pwdField').style.display = this.checked ? 'none' : 'block';">
              <label class="form-check-label" for="auto_password">Auto-generate a secure password</label>
            </div>

            <div class="mb-3" id="pwdField" style="display:{{ old('auto_password', true) ? 'none' : 'block' }};">
              <label class="form-label">Password</label>
              <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" placeholder="Minimum 8 characters">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Create admin &amp; finish</button>
              <a href="{{ route('admin.companies.index') }}" class="btn btn-light">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
