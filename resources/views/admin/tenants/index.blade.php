@extends('admin.layouts.master')

@section('title', 'Tenants')
@section('page-title', 'Tenants')
@section('breadcrumbs')<li class="breadcrumb-item active">Tenants</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Companies</h4>
          <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Tenant
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Name</th><th>Slug</th><th>Status</th>
                  <th class="text-end">Pages</th><th class="text-end">Collections</th><th class="text-end">Members</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($tenants as $tenant)
                  <tr>
                    <td class="f-w-600">{{ $tenant->name }}</td>
                    <td><code>{{ $tenant->slug }}</code></td>
                    <td>
                      <span class="badge badge-light-{{ $tenant->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($tenant->status) }}
                      </span>
                    </td>
                    <td class="text-end">{{ $tenant->pages_count }}</td>
                    <td class="text-end">{{ $tenant->collections_count }}</td>
                    <td class="text-end">{{ $tenant->memberships_count }}</td>
                    <td class="text-end">
                      <a href="{{ route('tenant.dashboard', $tenant) }}" class="btn btn-sm btn-outline-secondary me-1" title="Open company portal">
                        <i data-feather="external-link" style="width:15px;height:15px;"></i>
                      </a>
                      <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>

                      <form method="POST" action="{{ route('admin.tenants.reset-password', $tenant) }}" class="d-inline"
                            onsubmit="return confirm('Generate a new password for this company admin and email it?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning me-1" title="Reset admin password">
                          <i data-feather="key" style="width:15px;height:15px;"></i>
                        </button>
                      </form>

                      <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}" class="d-inline">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-outline-{{ $tenant->status === 'active' ? 'dark' : 'success' }} me-1"
                                title="{{ $tenant->status === 'active' ? 'Deactivate' : 'Activate' }}">
                          <i data-feather="{{ $tenant->status === 'active' ? 'slash' : 'check-circle' }}" style="width:15px;height:15px;"></i>
                        </button>
                      </form>

                      <x-admin.delete-button :action="route('admin.tenants.destroy', $tenant)" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-center text-muted py-4">No tenants yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $tenants->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
