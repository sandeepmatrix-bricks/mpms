@extends('company.layouts.master')

@section('title', 'Users')
@section('page-title', 'Users')
@section('breadcrumbs')<li class="breadcrumb-item active">Users</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Users</h4>
          @permission('users.write')
            <a href="{{ route('company.users.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add User
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="usersTable" class="table table-hover align-middle js-datatable" style="width:100%;">
              <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($members as $membership)
                  @php($user = $membership->user)
                  <tr>
                    <td class="f-w-600">
                      {{ $user?->name }}
                      @if ($user?->id === $currentUserId)<span class="badge badge-light-info ms-1">You</span>@endif
                    </td>
                    <td>{{ $user?->email }}</td>
                    <td>{{ $user?->phone ?? '—' }}</td>
                    <td><span class="badge badge-light-primary">{{ $membership->role?->name ?? '—' }}</span></td>
                    <td>
                      <span class="badge badge-light-{{ $user?->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user?->status ?? '') }}</span>
                      @if ($user?->must_change_password)<span class="badge badge-light-warning">pending login</span>@endif
                    </td>
                    <td class="text-end">
                      @permission('users.edit')
                        <a href="{{ route('company.users.edit', $user) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      @endpermission
                      @permission('users.delete')
                        <x-admin.delete-button :action="route('company.users.destroy', $user)" label="Remove user" />
                      @endpermission
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-4">No users yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
