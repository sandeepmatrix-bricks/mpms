@extends('company.layouts.master')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('breadcrumbs')<li class="breadcrumb-item active">Activity Logs</li>@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Activity Logs</h5>
          <small class="text-muted">Logins and key actions performed by your company's users.</small>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="logsTable" class="table table-hover align-middle js-datatable" style="width:100%;">
              <thead>
                <tr><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>When</th></tr>
              </thead>
              <tbody>
                @forelse ($logs as $log)
                  <tr>
                    <td class="f-w-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td><span class="badge badge-light-primary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                    <td>{{ $log->description ?? '—' }}</td>
                    <td>{{ $log->ip ?? '—' }}</td>
                    <td>{{ $log->created_at?->format('d M Y, h:i A') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted py-4">No activity yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
