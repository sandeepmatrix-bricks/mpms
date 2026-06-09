@extends('company.layouts.master')

@section('title', 'Roles')
@section('page-title', 'Roles & Permissions')
@section('breadcrumbs')<li class="breadcrumb-item active">Roles</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Roles &amp; Permissions</h4>
          @permission('roles.write')
            <a href="{{ route('company.roles.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add Role
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle js-datatable" style="width:100%;">
              <thead>
                <tr><th>Role</th><th class="text-end">Permissions</th><th class="text-end">Users</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($roles as $role)
                  <tr>
                    <td class="f-w-600">{{ $role->name }}</td>
                    <td class="text-end">{{ $role->permissions_count }}</td>
                    <td class="text-end">{{ $role->memberships_count }}</td>
                    <td class="text-end">
                      @permission('roles.edit')
                        <a href="{{ route('company.roles.edit', $role) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      @endpermission
                      @permission('roles.delete')
                        <x-admin.delete-button :action="route('company.roles.destroy', $role)" />
                      @endpermission
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
