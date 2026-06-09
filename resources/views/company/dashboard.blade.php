@extends('company.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumbs')<li class="breadcrumb-item active">Dashboard</li>@endsection

@push('page-styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
  <style>
    .dashboard-title { font-weight:700; color:#1e293b; margin-bottom:0; font-size:1.35rem; letter-spacing:-.3px; }
    .stat-card { border:none; border-radius:1rem; transition:transform .25s ease, box-shadow .25s ease; }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.09)!important; }
    .chart-card { border:none; border-radius:1rem; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .section-heading { font-weight:700; font-size:.95rem; color:#334155; letter-spacing:.2px; text-align:center; margin-bottom:1rem; }
    .fw-800 { font-weight:800; }
    .scope-pill { font-size:.72rem; font-weight:600; }
    #careerChart, #statusChart { min-height:330px; }
    #deptChart, #roleChart, #userActivityChart { min-height:340px; }
  </style>
@endpush

@php
  $cards = [
    ['emoji' => '🏢', 'label' => 'Total Department',      'value' => $stats['departments'],      'tone' => 'primary'],
    ['emoji' => '🎯', 'label' => 'Total Designation',     'value' => $stats['designations'],     'tone' => 'success'],
    ['emoji' => '📋', 'label' => 'Total Job Description',  'value' => $stats['job_descriptions'], 'tone' => 'danger'],
    ['emoji' => '👥', 'label' => 'Total Applicants',       'value' => $stats['applicants'],       'tone' => 'warning'],
  ];
@endphp

@section('content')
  <div class="mb-3">
    <h3 class="mb-0" style="font-weight:700;">Welcome, {{ auth()->user()->name }} 👋</h3>
    <p class="text-muted mb-0">
      {{ $company->name }} workspace
      @unless ($isMain)
        <span class="badge badge-light-warning scope-pill ms-1">Showing your assigned departments only</span>
      @endunless
    </p>
  </div>

  <div class="page-title d-flex align-items-center justify-content-between mb-3">
    <h3 class="dashboard-title">📊 Dashboard Overview</h3>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-4">
    @foreach ($cards as $card)
      <div class="col-md-3 col-sm-6">
        <div class="card stat-card text-center shadow-sm">
          <div class="card-body py-4">
            <div style="font-size:1.6rem;margin-bottom:6px;">{{ $card['emoji'] }}</div>
            <h6 class="text-muted mb-1">{{ $card['label'] }}</h6>
            <h2 class="text-{{ $card['tone'] }} fw-800 mb-0">{{ number_format($card['value']) }}</h2>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Charts Row 1 --}}
  <div class="row g-4 mt-2">
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Career Overview</p>
          <div id="careerChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Applicant Status</p>
          <div id="statusChart"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts Row 2 --}}
  <div class="row g-4 mt-2 mb-4">
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Department-wise Applicant Data</p>
          <div id="deptChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Job Role-wise Applicant Data</p>
          <div id="roleChart"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- User & Recent activity: MAIN (Admin) dashboard only --}}
  @if ($isMain)
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="card chart-card">
          <div class="card-body">
            <p class="section-heading mb-1">👤 User Activity</p>
            <p class="text-center text-muted" style="font-size:.8rem;">Comments &amp; status changes per team member</p>
            <div id="userActivityChart"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
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
  @endif
@endsection

@push('page-scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof ApexCharts === 'undefined') return;

      var noData = function (el) {
        document.querySelector(el).innerHTML = "<p class='text-center text-muted py-5'>No data yet</p>";
      };

      // Career Overview (bar): Departments / Designations / Job Descriptions
      new ApexCharts(document.querySelector('#careerChart'), {
        chart: { type: 'bar', height: 330, toolbar: { show: false } },
        series: [{ name: 'Total', data: [{{ $stats['departments'] }}, {{ $stats['designations'] }}, {{ $stats['job_descriptions'] }}] }],
        xaxis: { categories: ['Departments', 'Designations', 'Job Descriptions'] },
        plotOptions: { bar: { distributed: true, borderRadius: 8, columnWidth: '45%' } },
        colors: ['#4e73df', '#1cc88a', '#e74a3b'],
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
        legend: { show: false },
        grid: { borderColor: '#f1f5f9' },
      }).render();

      // Applicant Status (pie)
      var statusData = @json($statusChart);
      if (statusData.length > 0) {
        new ApexCharts(document.querySelector('#statusChart'), {
          chart: { type: 'pie', height: 330 },
          series: statusData.map(function (s) { return s.total; }),
          labels: statusData.map(function (s) { return s.label; }),
          colors: statusData.map(function (s) { return s.color; }),
          legend: { position: 'bottom', fontSize: '12px' },
        }).render();
      } else { noData('#statusChart'); }

      // Department-wise (donut)
      var deptData = @json($deptWise);
      if (deptData.length > 0) {
        new ApexCharts(document.querySelector('#deptChart'), {
          chart: { type: 'donut', height: 340 },
          series: deptData.map(function (d) { return d.total; }),
          labels: deptData.map(function (d) { return d.name; }),
          legend: { position: 'bottom', fontSize: '12px' },
        }).render();
      } else { noData('#deptChart'); }

      // Job Role-wise (donut)
      var roleData = @json($roleWise);
      if (roleData.length > 0) {
        new ApexCharts(document.querySelector('#roleChart'), {
          chart: { type: 'donut', height: 340 },
          series: roleData.map(function (r) { return r.total; }),
          labels: roleData.map(function (r) { return r.name; }),
          legend: { position: 'bottom', fontSize: '12px' },
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
            plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
            colors: ['#36b9cc', '#1cc88a'],
            dataLabels: { enabled: true, style: { fontSize: '11px', fontWeight: 600 } },
            legend: { position: 'top' },
            grid: { borderColor: '#f1f5f9' },
          }).render();
        } else { noData('#userActivityChart'); }
      @endif
    });
  </script>
@endpush
