@extends('admin.layouts.master')

@section('title', 'Records')
@section('page-title', 'Records')
@section('breadcrumbs')<li class="breadcrumb-item active">Records</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Records</h4>
          <a href="{{ route('admin.records.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Record
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Tenant</th><th>Collection</th><th>Data</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($records as $record)
                  <tr>
                    <td><span class="badge badge-light-info">{{ $record->collection?->tenant?->name }}</span></td>
                    <td class="f-w-600">{{ $record->collection?->name }}</td>
                    <td><code class="text-truncate d-inline-block" style="max-width:480px;">{{ json_encode($record->data, JSON_UNESCAPED_SLASHES) }}</code></td>
                    <td class="text-end">
                      <a href="{{ route('admin.records.edit', $record) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('admin.records.destroy', $record)" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-4">No records yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $records->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
