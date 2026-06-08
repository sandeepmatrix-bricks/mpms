@extends('tenant.layouts.master')

@section('title', 'Page Blocks')
@section('page-title', 'Page Blocks')
@section('breadcrumbs')<li class="breadcrumb-item active">Page Blocks</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Page Blocks</h4>
          <a href="{{ route('tenant.page-blocks.create', $tenant) }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Block
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle datatable" style="width:100%">
              <thead>
                <tr><th>Page</th><th>Type</th><th>Region</th><th class="text-end">Position</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @foreach ($blocks as $block)
                  <tr>
                    <td class="f-w-600">{{ $block->page?->title }}</td>
                    <td><span class="badge badge-light-primary">{{ ucfirst($block->type) }}</span></td>
                    <td>{{ $block->region }}</td>
                    <td class="text-end">{{ $block->position }}</td>
                    <td class="text-end">
                      <a href="{{ route('tenant.page-blocks.edit', [$tenant, $block]) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('tenant.page-blocks.destroy', [$tenant, $block])" />
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
