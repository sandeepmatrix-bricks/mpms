@extends('company.layouts.master')

@section('title', 'Designations')
@section('page-title', 'Designations')
@section('breadcrumbs')<li class="breadcrumb-item active">Designations</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Designations</h4>
          @permission('jobs.write')
            <a href="{{ route('company.job-listings.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add Designation
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="designationsTable" class="table table-hover align-middle js-datatable" style="width:100%;">
              <thead>
                <tr><th>Job Role</th><th>Department</th><th>Location</th><th>Status</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($listings as $listing)
                  <tr>
                    <td class="f-w-600">{{ $listing->job_role }}</td>
                    <td>{{ $listing->category?->name ?? '—' }}</td>
                    <td>{{ $listing->location ?? '—' }}</td>
                    <td>
                      <span class="badge badge-light-{{ $listing->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($listing->status) }}</span>
                    </td>
                    <td class="text-end">
                      @permission('jobs.edit')
                        <a href="{{ route('company.job-listings.edit', $listing) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      @endpermission
                      @permission('jobs.delete')
                        <x-admin.delete-button :action="route('company.job-listings.destroy', $listing)" label="Delete designation" />
                      @endpermission
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No designations yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
