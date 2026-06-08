@extends('admin.layouts.master')

@section('title', 'Permissions')
@section('page-title', 'Permissions')
@section('breadcrumbs')<li class="breadcrumb-item active">Permissions</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Permissions</h4>
          <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Permission
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Key</th><th class="text-end">Used by roles</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($permissions as $permission)
                  <tr>
                    <td><code>{{ $permission->key }}</code></td>
                    <td class="text-end">{{ $permission->roles_count }}</td>
                    <td class="text-end">
                      <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('admin.permissions.destroy', $permission)" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center text-muted py-4">No permissions yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $permissions->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
