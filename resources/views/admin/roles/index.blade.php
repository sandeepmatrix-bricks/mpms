@extends('admin.layouts.master')

@section('title', 'Roles')
@section('page-title', 'Roles')
@section('breadcrumbs')<li class="breadcrumb-item active">Roles</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Roles</h4>
          <a href="{{ route('admin.roles.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Role
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Name</th><th>Scope</th><th class="text-end">Permissions</th><th class="text-end">Members</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($roles as $role)
                  <tr>
                    <td class="f-w-600">{{ $role->name }}</td>
                    <td><span class="badge badge-light-{{ $role->scope === 'platform' ? 'primary' : 'info' }}">{{ ucfirst($role->scope) }}</span></td>
                    <td class="text-end">{{ $role->permissions_count }}</td>
                    <td class="text-end">{{ $role->memberships_count }}</td>
                    <td class="text-end">
                      <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('admin.roles.destroy', $role)" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No roles yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $roles->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
