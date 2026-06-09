@extends('company.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumbs')<li class="breadcrumb-item active">Dashboard</li>@endsection

@php
  $cards = [
    ['emoji' => '👥', 'label' => 'Users', 'value' => $stats['users'], 'tone' => 'primary', 'route' => 'company.users.index'],
    ['emoji' => '🛡️', 'label' => 'Roles', 'value' => $stats['roles'], 'tone' => 'success', 'route' => 'company.roles.index'],
  ];
@endphp

@section('content')
  <div class="mb-4">
    <h3 class="mb-0" style="font-weight:700;">Welcome, {{ auth()->user()->name }} 👋</h3>
    <p class="text-muted mb-0">{{ $company->name }} workspace</p>
  </div>

  <div class="row g-4">
    @foreach ($cards as $card)
      <div class="col-md-4 col-sm-6">
        @permission($card['label'] === 'Users' ? 'users.read' : 'roles.read')
          <a href="{{ route($card['route']) }}" class="text-decoration-none">
        @endpermission
        <div class="card text-center shadow-sm" style="border:none;border-radius:1rem;">
          <div class="card-body py-4">
            <div style="font-size:1.6rem;margin-bottom:6px;">{{ $card['emoji'] }}</div>
            <h6 class="text-muted mb-1">{{ $card['label'] }}</h6>
            <h2 class="text-{{ $card['tone'] }} mb-0" style="font-weight:800;">{{ number_format($card['value']) }}</h2>
          </div>
        </div>
        @permission($card['label'] === 'Users' ? 'users.read' : 'roles.read')
          </a>
        @endpermission
      </div>
    @endforeach
  </div>

  {{-- Recent activity --}}
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fa fa-history me-2"></i>Recent Activity</h5>
          @permission('users.read')
            <a href="{{ route('company.activity-logs.index') }}" class="btn btn-sm btn-primary">View all</a>
          @endpermission
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead><tr><th>User</th><th>Action</th><th>Details</th><th>When</th></tr></thead>
              <tbody>
                @forelse ($recentActivity as $log)
                  <tr>
                    <td class="f-w-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td><span class="badge badge-light-primary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                    <td>{{ $log->description ?? '—' }}</td>
                    <td class="text-muted">{{ $log->created_at?->diffForHumans() }}</td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted py-3">No activity yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
