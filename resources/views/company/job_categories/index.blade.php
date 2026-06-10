@extends('company.layouts.master')

@section('title', 'Departments')
@section('page-title', 'Job Departments')
@section('breadcrumbs')<li class="breadcrumb-item active">Departments</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Job Departments</h4>
          @permission('job_categories.write')
            <a href="{{ route('company.job-categories.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
              <i data-feather="plus-circle" style="width:16px;"></i> Add Department
            </a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="departmentsTable" class="table table-hover align-middle js-datatable" style="width:100%;">
              <thead>
                <tr><th>Department</th><th>Status</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($categories as $category)
                  <tr>
                    <td class="f-w-600">{{ $category->name }}</td>
                    <td>
                      <span class="badge badge-light-{{ $category->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($category->status) }}</span>
                    </td>
                    <td class="text-end">
                      @permission('job_categories.edit')
                        <a href="{{ route('company.job-categories.edit', $category) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      @endpermission
                      @permission('job_categories.delete')
                        <x-admin.delete-button :action="route('company.job-categories.destroy', $category)" label="Delete department" />
                      @endpermission
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center text-muted py-4">No departments yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
