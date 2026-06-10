@extends('company.layouts.master')

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
    .scope-pill { font-size:.72rem; font-weight:600; }

    /* KPI cards — flat, bordered, minimal */
    .kpi { background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:none; transition:border-color .2s ease, box-shadow .2s ease; }
    .kpi:hover { border-color:#d9e0e8; box-shadow:0 6px 18px rgba(15,23,42,.06); }
    .kpi .card-body { padding:1.3rem 1.35rem; }
    .kpi-ico { width:42px; height:42px; border-radius:11px; background:#eef6f5; color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:18px; }
    .kpi-val { font-size:1.9rem; font-weight:800; color:#0f172a; line-height:1; margin-top:1rem; }
    .kpi-lbl { color:#94a3b8; font-size:.73rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-top:.4rem; }

    /* Panels */
    .panel { background:#fff; border:1px solid var(--line); border-radius:14px; box-shadow:none; height:100%; }
    .panel-head { padding:.95rem 1.25rem; border-bottom:1px solid #f4f6f9; display:flex; align-items:center; gap:.55rem; }
    .panel-head i { color:#94a3b8; font-size:.9rem; }
    .panel-head h6 { margin:0; font-weight:700; color:#0f172a; font-size:.95rem; }
    .panel-body { padding:1rem 1.1rem 1.1rem; }

    .activity-table thead th { font-size:.73rem; text-transform:uppercase; letter-spacing:.4px; color:#94a3b8; border-bottom:1px solid #eef2f7; font-weight:600; }
    .activity-table td { vertical-align:middle; }
  </style>
@endpush

@php
  $tiles = [
    ['icon' => 'fa-building',       'label' => 'Departments',      'value' => $stats['departments']],
    ['icon' => 'fa-bullseye',       'label' => 'Designations',     'value' => $stats['designations']],
    ['icon' => 'fa-clipboard-list', 'label' => 'Job Descriptions', 'value' => $stats['job_descriptions']],
    ['icon' => 'fa-users',          'label' => 'Applicants',       'value' => $stats['applicants']],
  ];
  $panels = [
    ['id' => 'careerChart', 'title' => 'Career Overview',            'icon' => 'fa-chart-column'],
    ['id' => 'statusChart', 'title' => 'Applicant Status',           'icon' => 'fa-chart-pie'],
    ['id' => 'deptChart',   'title' => 'Department-wise Applicants', 'icon' => 'fa-sitemap'],
    ['id' => 'roleChart',   'title' => 'Job Role-wise Applicants',   'icon' => 'fa-briefcase'],
  ];
@endphp

@section('content')
<div class="dash-wrap">
  {{-- Header --}}
  <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-2">
    <div>
      <h4 class="dash-h">Dashboard</h4>
      <p class="dash-sub">
        {{ $company->name }} workspace
        @unless ($isMain)
          <span class="badge badge-light-warning scope-pill ms-1">Your assigned departments only</span>
        @endunless
      </p>
    </div>
    <div class="dash-date">{{ now()->format('l, d M Y') }}</div>
  </div>

  {{-- KPI cards --}}
  <div class="row g-3">
    @foreach ($tiles as $t)
      <div class="col-xl-3 col-md-6">
        <div class="card kpi h-100">
          <div class="card-body">
            <div class="kpi-ico"><i class="fa {{ $t['icon'] }}"></i></div>
            <div class="kpi-val">{{ number_format($t['value']) }}</div>
            <div class="kpi-lbl">{{ $t['label'] }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Charts Row 1 --}}
  <div class="row g-3 mt-1">
    @foreach (array_slice($panels, 0, 2) as $p)
      <div class="col-lg-6">
        <div class="card panel">
          <div class="panel-head"><i class="fa {{ $p['icon'] }}"></i><h6>{{ $p['title'] }}</h6></div>
          <div class="panel-body"><div id="{{ $p['id'] }}"></div></div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Charts Row 2 --}}
  <div class="row g-3 mt-1 mb-2">
    @foreach (array_slice($panels, 2, 2) as $p)
      <div class="col-lg-6">
        <div class="card panel">
          <div class="panel-head"><i class="fa {{ $p['icon'] }}"></i><h6>{{ $p['title'] }}</h6></div>
          <div class="panel-body"><div id="{{ $p['id'] }}"></div></div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- User & Recent activity: MAIN (Admin) dashboard only --}}
  @if ($isMain)
    <div class="row g-3 mt-1">
      <div class="col-12">
        <div class="card panel">
          <div class="panel-head">
            <i class="fa fa-user-group"></i><h6>User Activity</h6>
            <small class="text-muted ms-auto">Comments &amp; status changes per team member</small>
          </div>
          <div class="panel-body"><div id="userActivityChart"></div></div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1 mb-3">
      <div class="col-12">
        <div class="card panel">
          <div class="panel-head">
            <i class="fa fa-clock-rotate-left"></i><h6>Recent Activity</h6>
            @permission('users.read')
              <a href="{{ route('company.activity-logs.index') }}" class="btn btn-sm btn-outline-primary rounded-pill ms-auto px-3">View all</a>
            @endpermission
          </div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table activity-table align-middle mb-0">
                <thead><tr><th>User</th><th>Action</th><th>Details</th><th>When</th></tr></thead>
                <tbody>
                  @forelse ($recentActivity as $log)
                    <tr>
                      <td class="f-w-600">{{ $log->user?->name ?? 'System' }}</td>
                      <td><span class="badge rounded-pill badge-light-primary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
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
  @endif
</div>
@endsection

@push('page-scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof ApexCharts === 'undefined') return;
      // This theme can evaluate page scripts twice; render the charts only once.
      if (window.__companyDashboardRendered) return;
      window.__companyDashboardRendered = true;

      var noData = function (el) {
        document.querySelector(el).innerHTML = "<p class='text-center text-muted py-5'>No data yet</p>";
      };

      // Career Overview (bar): Departments / Designations / Job Descriptions
      new ApexCharts(document.querySelector('#careerChart'), {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [{ name: 'Total', data: [{{ $stats['departments'] }}, {{ $stats['designations'] }}, {{ $stats['job_descriptions'] }}] }],
        xaxis: { categories: ['Departments', 'Designations', 'Job Descriptions'] },
        plotOptions: { bar: { distributed: true, borderRadius: 6, columnWidth: '42%' } },
        colors: ['#4e73df', '#1cc88a', '#e74a3b'],
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
        legend: { show: false },
        grid: { borderColor: '#f4f6f9' },
      }).render();

      // Applicant Status (donut)
      var statusData = @json($statusChart);
      if (statusData.length > 0) {
        new ApexCharts(document.querySelector('#statusChart'), {
          chart: { type: 'donut', height: 320 },
          series: statusData.map(function (s) { return s.total; }),
          labels: statusData.map(function (s) { return s.label; }),
          colors: statusData.map(function (s) { return s.color; }),
          legend: { position: 'bottom', fontSize: '12px' },
          plotOptions: { pie: { donut: { size: '66%' } } },
        }).render();
      } else { noData('#statusChart'); }

      // Department-wise (donut)
      var deptData = @json($deptWise);
      if (deptData.length > 0) {
        new ApexCharts(document.querySelector('#deptChart'), {
          chart: { type: 'donut', height: 330 },
          series: deptData.map(function (d) { return d.total; }),
          labels: deptData.map(function (d) { return d.name; }),
          legend: { position: 'bottom', fontSize: '12px' },
          plotOptions: { pie: { donut: { size: '66%' } } },
        }).render();
      } else { noData('#deptChart'); }

      // Job Role-wise (donut)
      var roleData = @json($roleWise);
      if (roleData.length > 0) {
        new ApexCharts(document.querySelector('#roleChart'), {
          chart: { type: 'donut', height: 330 },
          series: roleData.map(function (r) { return r.total; }),
          labels: roleData.map(function (r) { return r.name; }),
          legend: { position: 'bottom', fontSize: '12px' },
          plotOptions: { pie: { donut: { size: '66%' } } },
        }).render();
      } else { noData('#roleChart'); }

      @if ($isMain)
        // User Activity (grouped bar) — main dashboard only
        var ua = @json($userActivity);
        if (ua.length > 0) {
          new ApexCharts(document.querySelector('#userActivityChart'), {
            chart: { type: 'bar', height: 340, toolbar: { show: false } },
            series: [
              { name: 'Comments', data: ua.map(function (u) { return u.comments; }) },
              { name: 'Status Changes', data: ua.map(function (u) { return u.changes; }) },
            ],
            xaxis: { categories: ua.map(function (u) { return u.name; }) },
            plotOptions: { bar: { borderRadius: 5, columnWidth: '55%' } },
            colors: ['#36b9cc', '#1cc88a'],
            dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 600 } },
            legend: { position: 'top' },
            grid: { borderColor: '#f4f6f9' },
          }).render();
        } else { noData('#userActivityChart'); }
      @endif
    });
  </script>
@endpush
