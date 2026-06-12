@extends('company.layouts.master')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('breadcrumbs')<li class="breadcrumb-item active">Settings</li>@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h4 class="mb-0">Company Profile</h4></div>
        <div class="card-body">
          <form method="POST" action="{{ route('company.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $company->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone) }}">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $company->address) }}">
              </div>
            </div>
            <div class="mt-3">
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Company users: the owner allocated by the Super Admin + users created inside the company. --}}
      <div class="card">
        <div class="card-header"><h4 class="mb-0"><i class="fa fa-users me-2"></i>Company Users</h4></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Type</th></tr>
              </thead>
              <tbody>
                @forelse ($members as $i => $member)
                  <tr>
                    <td class="f-w-600">{{ $member->user?->name ?? '—' }}</td>
                    <td>{{ $member->user?->email ?? '—' }}</td>
                    <td>{{ $member->user?->phone ?? '—' }}</td>
                    <td>{{ $member->role?->name ?? '—' }}</td>
                    <td>
                      <span class="badge badge-light-{{ ($member->user?->status ?? 'active') === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($member->user?->status ?? 'active') }}
                      </span>
                    </td>
                    <td>
                      @if ($i === 0)
                        <span class="badge badge-light-primary">Owner (Super Admin)</span>
                      @else
                        <span class="badge badge-light-info">Company User</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-3">No users yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
