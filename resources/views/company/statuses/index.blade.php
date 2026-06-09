@extends('company.layouts.master')

@section('title', 'Application Statuses')
@section('page-title', 'Application Statuses')
@section('breadcrumbs')<li class="breadcrumb-item active">Statuses</li>@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">Application Statuses</h5>
            <small class="text-muted">These drive the Status dropdown on applicants. Add your own below.</small>
          </div>
          @permission('statuses.write')
            <a href="{{ route('company.statuses.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Add Status</a>
          @endpermission
        </div>
        <div class="card-body">
          <h6 class="text-muted">Your statuses</h6>
          <div class="table-responsive mb-4">
            <table class="table table-hover align-middle">
              <thead><tr><th>Status</th><th>Color</th><th>Order</th><th>Active</th><th class="text-end">Actions</th></tr></thead>
              <tbody>
                @forelse ($custom as $status)
                  <tr>
                    <td><span class="badge" style="background:{{ $status->color }};color:#fff;">{{ $status->label }}</span></td>
                    <td><code>{{ $status->color }}</code></td>
                    <td>{{ $status->sort_order }}</td>
                    <td>{!! $status->is_active ? '<span class="badge badge-light-success">Active</span>' : '<span class="badge badge-light-secondary">Hidden</span>' !!}</td>
                    <td class="text-end">
                      @permission('statuses.edit')
                        <a href="{{ route('company.statuses.edit', $status) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                      @endpermission
                      @permission('statuses.delete')
                        <x-admin.delete-button :action="route('company.statuses.destroy', $status)" label="Remove status" />
                      @endpermission
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-3">No custom statuses yet — add one to extend the dropdown.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <h6 class="text-muted">Default statuses (shared, read-only)</h6>
          <div class="d-flex flex-wrap gap-2">
            @foreach ($defaults as $status)
              <span class="badge" style="background:{{ $status->color }};color:#fff;">{{ $status->label }}</span>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
