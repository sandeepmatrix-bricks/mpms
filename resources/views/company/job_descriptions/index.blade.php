@extends('company.layouts.master')

@section('title', 'Job Descriptions')
@section('page-title', 'Job Descriptions')
@section('breadcrumbs')<li class="breadcrumb-item active">Job Descriptions</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Job Descriptions</h4>
          @permission('jobs.write')
            <a href="{{ route('company.job-descriptions.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add Job Description
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="descriptionsTable" class="table table-hover align-middle js-datatable" style="width:100%;">
              <thead>
                <tr><th>Designation</th><th>Job Role</th><th>Location</th><th>Status</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($descriptions as $description)
                  <tr>
                    <td class="f-w-600">{{ $description->designation ?? '—' }}</td>
                    <td>{{ $description->listing?->job_role ?? '—' }}</td>
                    <td>{{ $description->location ?? '—' }}</td>
                    <td>
                      <span class="badge badge-light-{{ $description->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($description->status) }}</span>
                    </td>
                    <td class="text-end">
                      @permission('jobs.edit')
                        <a href="{{ route('company.job-descriptions.edit', $description) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      @endpermission
                      @permission('jobs.delete')
                        <x-admin.delete-button :action="route('company.job-descriptions.destroy', $description)" label="Delete job description" />
                      @endpermission
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No job descriptions yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
