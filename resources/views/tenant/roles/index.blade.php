@extends('tenant.layouts.master')

@section('title', 'Roles')
@section('page-title', 'Roles')
@section('breadcrumbs')<li class="breadcrumb-item active">Roles</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Roles &amp; Permissions</h4>
          <a href="{{ route('tenant.roles.create', $tenant) }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Role
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Role</th><th class="text-end">Permissions</th><th class="text-end">Users</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($roles as $role)
                  <tr>
                    <td class="f-w-600">{{ $role->name }}</td>
                    <td class="text-end">{{ $role->permissions_count }}</td>
                    <td class="text-end">{{ $role->memberships_count }}</td>
                    <td class="text-end">
                      <a href="{{ route('tenant.roles.edit', [$tenant, $role]) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('tenant.roles.destroy', [$tenant, $role])" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-4">No roles yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
