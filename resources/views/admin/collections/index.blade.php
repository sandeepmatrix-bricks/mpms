@extends('admin.layouts.master')

@section('title', 'Collections')
@section('page-title', 'Collections')
@section('breadcrumbs')<li class="breadcrumb-item active">Collections</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Collections</h4>
          <a href="{{ route('admin.collections.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Collection
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr><th>Tenant</th><th>Name</th><th>Key</th><th class="text-end">Records</th><th class="text-end">Actions</th></tr>
              </thead>
              <tbody>
                @forelse ($collections as $collection)
                  <tr>
                    <td><span class="badge badge-light-info">{{ $collection->tenant?->name }}</span></td>
                    <td class="f-w-600">{{ $collection->name }}</td>
                    <td><code>{{ $collection->key }}</code></td>
                    <td class="text-end">{{ $collection->records_count }}</td>
                    <td class="text-end">
                      <a href="{{ route('admin.collections.edit', $collection) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('admin.collections.destroy', $collection)" />
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No collections yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          {{ $collections->links() }}
        </div>
      </div>
    </div>
  </div>
@endsection
