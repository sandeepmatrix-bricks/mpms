@extends('admin.layouts.master')

@section('title', 'New Company')
@section('page-title', 'New Company')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
  <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Register Company</h4>
          <small class="text-muted">Add the company details. Create its login afterwards under <strong>Users</strong>.</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.companies.store') }}">
            @csrf
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number') }}" placeholder="e.g. 27AAAAA0000A1Z5">
              </div>
            </div>
            <div class="row">
              <div class="col-md-8 mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', 'active') === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Register Company</button>
              <a href="{{ route('admin.companies.index') }}" class="btn btn-light">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
