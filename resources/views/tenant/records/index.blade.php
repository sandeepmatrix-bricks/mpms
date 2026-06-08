@extends('tenant.layouts.master')

@section('title', 'Records')
@section('page-title', 'Records')
@section('breadcrumbs')<li class="breadcrumb-item active">Records</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Records</h4>
          <a href="{{ route('tenant.records.create', $tenant) }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Record
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle datatable" style="width:100%">
              <thead>
                <tr><th>Collection</th><th>Data</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @foreach ($records as $record)
                  <tr>
                    <td class="f-w-600">{{ $record->collection?->name }}</td>
                    <td><code class="text-truncate d-inline-block" style="max-width:520px;">{{ json_encode($record->data, JSON_UNESCAPED_SLASHES) }}</code></td>
                    <td class="text-end">
                      <a href="{{ route('tenant.records.edit', [$tenant, $record]) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('tenant.records.destroy', [$tenant, $record])" />
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
