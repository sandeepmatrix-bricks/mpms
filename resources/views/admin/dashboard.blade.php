@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumbs')<li class="breadcrumb-item active">Dashboard</li>@endsection

@push('page-styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
  <style>
    .dash-wrap { --accent:#0d6e6e; --line:#edf0f4; }
    .dash-h { font-weight:700; color:#0f172a; margin:0; letter-spacing:-.3px; }
    .dash-sub { color:#94a3b8; margin:0; font-size:.9rem; }
    .dash-date { color:#94a3b8; font-size:.85rem; font-weight:600; }

    .kpi { background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:none; transition:border-color .2s ease, box-shadow .2s ease; }
    .kpi:hover { border-color:#d9e0e8; box-shadow:0 6px 18px rgba(15,23,42,.06); }
    .kpi .card-body { padding:1.3rem 1.35rem; }
    .kpi-ico { width:42px; height:42px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:18px; }
    .kpi-val { font-size:1.9rem; font-weight:800; color:#0f172a; line-height:1; margin-top:1rem; }
    .kpi-lbl { color:#94a3b8; font-size:.73rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-top:.4rem; }
    .soft-teal { background:#eef6f5; color:#0d6e6e; }
    .soft-green { background:rgba(28,200,138,.14); color:#1cc88a; }
    .soft-amber { background:rgba(246,194,62,.18); color:#e0a800; }

    .panel { background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:none; height:100%; }
    .panel-head { padding:.95rem 1.25rem; border-bottom:1px solid #f4f6f9; display:flex; align-items:center; gap:.55rem; }
    .panel-head i { color:#94a3b8; font-size:.9rem; }
    .panel-head h6 { margin:0; font-weight:700; color:#0f172a; font-size:.95rem; }
    .panel-body { padding:1rem 1.1rem 1.1rem; }

    .dash-table thead th { font-size:.73rem; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8; border-bottom:1px solid #eef2f7; font-weight:600; }
    .dash-table td { vertical-align:middle; }
    .user-chip { display:inline-flex; align-items:center; gap:.5rem; }
    .user-chip .av { width:30px; height:30px; border-radius:50%; background:#0d6e6e; color:#fff; font-size:.75rem; font-weight:700; display:flex; align-items:center; justify-content:center; }
  </style>
@endpush

@php
  $tiles = [
    ['icon' => 'fa-building',     'label' => 'Companies', 'value' => $stats['companies'], 'soft' => 'soft-teal'],
    ['icon' => 'fa-circle-check', 'label' => 'Active',    'value' => $stats['active'],    'soft' => 'soft-green'],
    ['icon' => 'fa-circle-pause', 'label' => 'Inactive',  'value' => $stats['inactive'],  'soft' => 'soft-amber'],
  ];
@endphp

@section('content')
<div class="dash-wrap">
  {{-- Header --}}
  <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
      <h4 class="dash-h">Platform Dashboard</h4>
      <p class="dash-sub">Overview of every company on the platform</p>
    </div>
    <div class="dash-date">{{ now()->format('l, d M Y') }}</div>
  </div>

  {{-- KPI cards (companies only — no user counts) --}}
  <div class="row g-3">
    @foreach ($tiles as $t)
      <div class="col-md-4 col-sm-6">
        <div class="card kpi h-100">
          <div class="card-body">
            <div class="kpi-ico {{ $t['soft'] }}"><i class="fa {{ $t['icon'] }}"></i></div>
            <div class="kpi-val">{{ number_format($t['value']) }}</div>
            <div class="kpi-lbl">{{ $t['label'] }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Charts (side by side in one row) --}}
  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="card panel">
        <div class="panel-head"><i class="fa fa-chart-area"></i><h6>Company Growth — last 6 months</h6></div>
        <div class="panel-body"><div id="growthChart" style="min-height:300px;"></div></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card panel">
        <div class="panel-head"><i class="fa fa-chart-pie"></i><h6>Company Status</h6></div>
        <div class="panel-body"><div id="statusChart" style="min-height:300px;"></div></div>
      </div>
    </div>
  </div>

  {{-- Company → User mapping --}}
  <div class="row g-3 mt-1">
    <div class="col-12">
      <div class="card panel">
        <div class="panel-head">
          <i class="fa fa-building-user"></i><h6>Companies &amp; Assigned Users</h6>
          @permission('companies.write')
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary ms-auto">
              <i data-feather="plus-circle" style="width:15px;"></i> Add Company
            </a>
          @endpermission
        </div>
        <div class="panel-body">
          <div class="table-responsive">
            <table class="table dash-table align-middle mb-0">
              <thead><tr><th>Company</th><th>Email</th><th>Assigned User</th><th>Status</th></tr></thead>
              <tbody>
                @forelse ($companies as $company)
                  <tr>
                    <td class="f-w-600">{{ $company->name }}</td>
                    <td class="text-muted">{{ $company->email ?? '—' }}</td>
                    <td>
                      @if ($company->ownerMembership?->user)
                        <span class="user-chip">
                          <span class="av">{{ strtoupper(substr($company->ownerMembership->user->name, 0, 1)) }}</span>
                          {{ $company->ownerMembership->user->name }}
                        </span>
                      @else
                        <span class="text-muted">— not allocated —</span>
                      @endif
                    </td>
                    <td><span class="badge rounded-pill badge-light-{{ $company->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($company->status) }}</span></td>
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

  {{-- Platform-wide activity --}}
  <div class="row g-3 mt-1 mb-3">
    <div class="col-12">
      <div class="card panel">
        <div class="panel-head"><i class="fa fa-clock-rotate-left"></i><h6>Recent Activity (All Companies)</h6></div>
        <div class="panel-body">
          <div class="table-responsive">
            <table class="table dash-table align-middle mb-0">
              <thead><tr><th>User</th><th>Company</th><th>Action</th><th>Details</th><th>When</th></tr></thead>
              <tbody>
                @forelse ($recentActivity as $log)
                  <tr>
                    <td class="f-w-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="text-muted">{{ $log->tenant?->name ?? '—' }}</td>
                    <td><span class="badge rounded-pill badge-light-primary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
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
</div>
@endsection

@push('page-scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof ApexCharts === 'undefined') return;
      if (window.__adminDashboardRendered) return;
      window.__adminDashboardRendered = true;

      // Growth (area)
      var signups = @json($signups);
      new ApexCharts(document.querySelector('#growthChart'), {
        chart: { type: 'area', height: 300, toolbar: { show: false } },
        series: [{ name: 'New companies', data: signups.map(function (s) { return s.total; }) }],
        xaxis: { categories: signups.map(function (s) { return s.label; }) },
        colors: ['#0d6e6e'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .35, opacityTo: .05 } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f4f6f9' },
      }).render();

      // Status (donut)
      new ApexCharts(document.querySelector('#statusChart'), {
        chart: { type: 'donut', height: 300 },
        series: [{{ $stats['active'] }}, {{ $stats['inactive'] }}],
        labels: ['Active', 'Inactive'],
        colors: ['#1cc88a', '#f6c23e'],
        legend: { position: 'bottom' },
        plotOptions: { pie: { donut: { size: '68%' } } },
      }).render();
    });
  </script>
@endpush
