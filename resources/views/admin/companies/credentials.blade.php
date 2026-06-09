@extends('admin.layouts.master')

@section('title', 'Company Ready')
@section('page-title', 'Company Ready')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
  <li class="breadcrumb-item active">Credentials</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body text-center">
          <div style="font-size:2.4rem;">✅</div>
          <h3 class="mt-2 mb-1">{{ $company->name }} is ready!</h3>
          <p class="text-muted">Share these login details with the company admin. They'll set their own password on first login. (A copy was also emailed.)</p>

          <div class="text-start mx-auto" style="max-width:420px;">
            <div class="alert alert-light border mb-2 d-flex justify-content-between align-items-center">
              <span><strong>Login URL</strong></span>
              <code>{{ url('/company/login') }}</code>
            </div>
            <div class="alert alert-light border mb-2 d-flex justify-content-between align-items-center">
              <span><strong>Email</strong></span>
              <code id="cred-email">{{ $credentials['email'] }}</code>
            </div>
            <div class="alert alert-light border d-flex justify-content-between align-items-center">
              <span><strong>Password</strong></span>
              <code id="cred-pass" style="font-size:15px;">{{ $credentials['password'] }}</code>
            </div>
          </div>

          <div class="mt-3">
            <button class="btn btn-outline-secondary me-1" onclick="copyCreds()">
              <i data-feather="copy" style="width:15px;"></i> Copy
            </button>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-primary">Done</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function copyCreds() {
      const text = 'URL: {{ url('/company/login') }}\nEmail: ' +
        document.getElementById('cred-email').innerText +
        '\nPassword: ' + document.getElementById('cred-pass').innerText;
      navigator.clipboard.writeText(text);
    }
  </script>
@endsection
