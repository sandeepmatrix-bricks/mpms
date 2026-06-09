@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumbs')<li class="breadcrumb-item active">Dashboard</li>@endsection

@php
  $cards = [
    ['emoji' => '🏢', 'label' => 'Companies', 'value' => $stats['companies'], 'tone' => 'primary'],
    ['emoji' => '✅', 'label' => 'Active',    'value' => $stats['active'],    'tone' => 'success'],
    ['emoji' => '⏸️', 'label' => 'Inactive',  'value' => $stats['inactive'],  'tone' => 'secondary'],
    ['emoji' => '👥', 'label' => 'Users',     'value' => $stats['users'],     'tone' => 'info'],
  ];
@endphp

@section('content')
  <div class="row g-4">
    @foreach ($cards as $card)
      <div class="col-md-3 col-sm-6">
        <div class="card text-center shadow-sm" style="border:none;border-radius:1rem;">
          <div class="card-body py-4">
            <div style="font-size:1.6rem;margin-bottom:6px;">{{ $card['emoji'] }}</div>
            <h6 class="text-muted mb-1">{{ $card['label'] }}</h6>
            <h2 class="text-{{ $card['tone'] }} mb-0" style="font-weight:800;">{{ number_format($card['value']) }}</h2>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="row mt-2">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Recent Companies</h4>
          <a href="{{ route('admin.companies.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
            <i data-feather="plus-circle" style="width:16px;"></i> Add Company
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead><tr><th>Company</th><th>Email</th><th>Status</th><th class="text-end">Users</th></tr></thead>
              <tbody>
                @forelse ($companies as $company)
                  <tr>
                    <td class="f-w-600">{{ $company->name }}</td>
                    <td>{{ $company->email ?? '—' }}</td>
                    <td><span class="badge badge-light-{{ $company->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($company->status) }}</span></td>
                    <td class="text-end">{{ $company->memberships_count }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-4">No companies yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Platform-wide activity (all companies) --}}
  <div class="row mt-2">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header"><h4 class="mb-0"><i class="fa fa-history me-2"></i>Recent Activity (All Companies)</h4></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead><tr><th>User</th><th>Company</th><th>Action</th><th>Details</th><th>When</th></tr></thead>
              <tbody>
                @forelse ($recentActivity as $log)
                  <tr>
                    <td class="f-w-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td>{{ $log->tenant?->name ?? '—' }}</td>
                    <td><span class="badge badge-light-primary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                    <td>{{ $log->description ?? '—' }}</td>
                    <td class="text-muted">{{ $log->created_at?->diffForHumans() }}</td>
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
