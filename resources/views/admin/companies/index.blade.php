@extends('admin.layouts.master')

@section('title', 'Companies')
@section('page-title', 'Companies')
@section('breadcrumbs')<li class="breadcrumb-item active">Companies</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Companies</h4>
          @permission('companies.write')
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add Company
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Company</th><th>Email</th><th>Status</th><th>User</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($companies as $company)
                  <tr>
                    <td class="f-w-600">{{ $company->name }}</td>
                    <td>{{ $company->email ?? '—' }}</td>
                    <td>
                      <span class="badge badge-light-{{ $company->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($company->status) }}</span>
                    </td>
                    {{-- The one user the Super Admin allocated (the company owner). --}}
                    <td>
                      @if ($company->ownerMembership?->user)
                        {{ $company->ownerMembership->user->name }}
                      @else
                        <span class="text-muted">— not allocated —</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                        @permission('companies.edit')
                          <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-primary">Edit</a>
                        @endpermission
                        @permission('companies.deactivate')
                          <form method="POST" action="{{ route('admin.companies.toggle-status', $company) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-{{ $company->status === 'active' ? 'dark' : 'success' }}">
                              {{ $company->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                          </form>
                        @endpermission
                        @permission('companies.delete')
                          <x-admin.delete-button :action="route('admin.companies.destroy', $company)" label="Delete company" />
                        @endpermission
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No companies yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $companies->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
