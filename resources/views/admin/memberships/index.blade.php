@extends('admin.layouts.master')

@section('title', 'Memberships')
@section('page-title', 'Memberships')
@section('breadcrumbs')<li class="breadcrumb-item active">Memberships</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Memberships</h4>
          <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Assign Membership
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>User</th><th>Scope</th><th>Role</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($memberships as $membership)
                  <tr>
                    <td>
                      <span class="f-w-600">{{ $membership->user?->name }}</span>
                      <div class="text-muted small">{{ $membership->user?->email }}</div>
                    </td>
                    <td>
                      @if ($membership->tenant)
                        <span class="badge badge-light-info">{{ $membership->tenant->name }}</span>
                      @else
                        <span class="badge badge-light-primary">Platform (all tenants)</span>
                      @endif
                    </td>
                    <td>{{ $membership->role?->name }}</td>
                    <td class="text-end">
                      <a href="{{ route('admin.memberships.edit', $membership) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      <x-admin.delete-button :action="route('admin.memberships.destroy', $membership)" label="Remove" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-4">No memberships yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $memberships->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
