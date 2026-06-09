@extends('admin.layouts.master')

@section('title', 'Users')
@section('page-title', 'Users')
@section('breadcrumbs')<li class="breadcrumb-item active">Users</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Company Users</h4>
          @permission('users.write')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add User
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Status</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($users as $user)
                  @php($membership = $user->memberships->first())
                  <tr>
                    <td class="f-w-600">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td><span class="badge badge-light-info">{{ $membership?->tenant?->name ?? '—' }}</span></td>
                    <td><span class="badge badge-light-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span></td>
                    <td class="text-end">
                      <div class="d-inline-flex gap-1 justify-content-end">
                        @permission('users.edit')
                          <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @endpermission
                        @permission('users.delete')
                          <x-admin.delete-button :action="route('admin.users.destroy', $user)" label="Remove user" />
                        @endpermission
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-4">No users yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
