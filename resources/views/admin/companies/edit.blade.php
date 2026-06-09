@extends('admin.layouts.master')

@section('title', 'Edit Company')
@section('page-title', 'Edit Company')
@section('breadcrumbs')
  <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
  <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.companies.update', $company) }}">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $company->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">GST Number</label>
                <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $company->gst_number) }}">
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $company->status) === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Save Company</button>
              <a href="{{ route('admin.companies.index') }}" class="btn btn-light">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
