@extends('tenant.layouts.master')

@section('title', 'Team')
@section('page-title', 'Team')
@section('breadcrumbs')<li class="breadcrumb-item active">Team</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Users</h4>
          <a href="{{ route('tenant.users.create', $tenant) }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add User
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($memberships as $membership)
                  @php($user = $membership->user)
                  <tr>
                    <td class="f-w-600">{{ $user?->name }}</td>
                    <td>{{ $user?->email }}</td>
                    <td><span class="badge badge-light-primary">{{ $membership->role?->name ?? '—' }}</span></td>
                    <td>
                      <span class="badge badge-light-{{ $user?->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($user?->status ?? 'unknown') }}
                      </span>
                      @if ($user?->must_change_password)
                        <span class="badge badge-light-warning" title="Has not set their own password yet">pending first login</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <a href="{{ route('tenant.users.edit', [$tenant, $user]) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('tenant.users.destroy', [$tenant, $user])" label="Remove user" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No users yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
