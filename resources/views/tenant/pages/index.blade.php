@extends('tenant.layouts.master')

@section('title', 'Pages')
@section('page-title', 'Pages')
@section('breadcrumbs')<li class="breadcrumb-item active">Pages</li>@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Pages</h4>
          <a href="{{ route('tenant.pages.create', $tenant) }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Page
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle datatable" style="width:100%">
              <thead>
                <tr><th>Title</th><th>Slug</th><th>Parent</th><th>Layout</th><th>Active</th><th class="text-end">Blocks</th><th class="text-end no-sort">Actions</th></tr>
              </thead>
              <tbody>
                @foreach ($pages as $page)
                  <tr>
                    <td class="f-w-600">{{ $page->title }}</td>
                    <td><code>{{ $page->slug }}</code></td>
                    <td>{{ $page->parent?->title ?? '&mdash;' }}</td>
                    <td>{{ $page->layout }}</td>
                    <td><span class="badge badge-light-{{ $page->is_active ? 'success' : 'secondary' }}">{{ $page->is_active ? 'Yes' : 'No' }}</span></td>
                    <td class="text-end">{{ $page->page_blocks_count }}</td>
                    <td class="text-end">
                      <a href="{{ route('tenant.pages.edit', [$tenant, $page]) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i data-feather="edit-2" style="width:15px;height:15px;"></i>
                      </a>
                      <x-admin.delete-button :action="route('tenant.pages.destroy', [$tenant, $page])" />
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
