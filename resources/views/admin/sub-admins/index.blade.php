@extends('admin.layouts.master')

@section('title', 'Sub Admins')
@section('page-title', 'Sub Admins')
@section('breadcrumbs')<li class="breadcrumb-item active">Sub Admins</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Sub Admins</h4>
          @permission('sub_admins.write')
            <a href="{{ route('admin.sub-admins.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add Sub Admin
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($subAdmins as $sub)
                  @php($role = $sub->memberships->first()?->role)
                  <tr>
                    <td class="f-w-600">{{ $sub->name }}</td>
                    <td>{{ $sub->email }}</td>
                    <td><span class="badge badge-light-primary">{{ $role?->name ?? '—' }}</span></td>
                    <td><span class="badge badge-light-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sub->status) }}</span></td>
                    <td class="text-end">
                      <div class="d-inline-flex gap-1 justify-content-end">
                        @permission('sub_admins.edit')
                          <a href="{{ route('admin.sub-admins.edit', $sub) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endpermission
                        @permission('sub_admins.delete')
                          <x-admin.delete-button :action="route('admin.sub-admins.destroy', $sub)" label="Remove sub admin" />
                        @endpermission
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No sub admins yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $subAdmins->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
