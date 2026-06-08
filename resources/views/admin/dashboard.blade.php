@extends('admin.layouts.master')

@section('title', 'Platform Dashboard')
@section('page-title', 'Platform Dashboard')

@section('breadcrumbs')
  <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('page-styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
  <style>
    .dashboard-title { font-weight:700; color:#1e293b; margin-bottom:0; font-size:1.35rem; letter-spacing:-.3px; }
    .stat-card { border:none; border-radius:1rem; transition:transform .25s ease, box-shadow .25s ease; }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.09)!important; }
    .chart-card { border:none; border-radius:1rem; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .section-heading { font-weight:700; font-size:.88rem; color:#475569; letter-spacing:.3px; text-align:center; margin-bottom:1rem; }
    .fw-800 { font-weight:800; }
    #overviewChart, #typeChart { min-height:330px; }
    #recordsChart, #pagesChart { min-height:350px; }
  </style>
@endpush

@php
  $cards = [
    ['emoji' => '🏢', 'label' => 'Companies',   'value' => $stats['tenants'], 'tone' => 'primary'],
    ['emoji' => '👥', 'label' => 'Users',       'value' => $stats['users'],   'tone' => 'success'],
    ['emoji' => '📄', 'label' => 'Pages',       'value' => $stats['pages'],   'tone' => 'danger'],
    ['emoji' => '🗃️', 'label' => 'Records',     'value' => $stats['records'], 'tone' => 'warning'],
  ];
@endphp

@section('content')
  <div class="page-title d-flex align-items-center justify-content-between mb-4">
    <h3 class="dashboard-title">📊 Platform Overview</h3>
    <span class="badge badge-light-primary">{{ $tenants->count() }} companies</span>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-4">
    @foreach ($cards as $card)
      <div class="col-md-3 col-sm-6">
        <div class="card stat-card text-center shadow-sm">
          <div class="card-body py-4">
            <div style="font-size:1.6rem;margin-bottom:6px;">{{ $card['emoji'] }}</div>
            <h6 class="text-muted fw-600 mb-1">{{ $card['label'] }}</h6>
            <h2 class="text-{{ $card['tone'] }} fw-800 mb-0">{{ number_format($card['value']) }}</h2>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Charts Row 1 --}}
  <div class="row g-4 mt-2">
    <div class="col-lg-6">
      <div class="card chart-card">
        <div class="card-body">
          <p class="section-heading">Platform Overview</p>
          <div id="overviewChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card">
        <div class="card-body">
          <p class="section-heading">Records per Company</p>
          <div id="recordsChart"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts Row 2 --}}
  <div class="row g-4 mt-2">
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Pages per Company</p>
          <div id="pagesChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Sections by Type</p>
          <div id="typeChart"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Companies table --}}
  <div class="row mt-2 mb-4">
    <div class="col-sm-12">
      <div class="card chart-card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="mb-0">Companies</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Company</th><th>Slug</th><th>Status</th>
                  <th class="text-end">Pages</th><th class="text-end">Collections</th><th class="text-end">Records</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($tenants as $tenant)
                  <tr>
                    <td class="f-w-600">{{ $tenant->name }}</td>
                    <td><code>{{ $tenant->slug }}</code></td>
                    <td>
                      <span class="badge badge-light-{{ $tenant->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($tenant->status) }}</span>
                    </td>
                    <td class="text-end">{{ $tenant->pages_count }}</td>
                    <td class="text-end">{{ $tenant->collections_count }}</td>
                    <td class="text-end">{{ $tenant->records_count }}</td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted py-4">No tenants yet.</td></tr>
                @endforelse
              </tbody>
            </table>
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

      // Platform Overview bar
      new ApexCharts(document.querySelector('#overviewChart'), {
        chart: { type: 'bar', height: 330, toolbar: { show: false } },
        series: [{ name: 'Total', data: [{{ $stats['tenants'] }}, {{ $stats['users'] }}, {{ $stats['pages'] }}, {{ $stats['collections'] }}, {{ $stats['records'] }}] }],
        xaxis: { categories: ['Companies', 'Users', 'Pages', 'Collections', 'Records'] },
        plotOptions: { bar: { distributed: true, borderRadius: 8, columnWidth: '50%' } },
        colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
        legend: { show: false },
        grid: { borderColor: '#f1f5f9' },
      }).render();

      var companyLabels = @json($tenants->pluck('name'));

      // Records per Company donut
      var recordSeries = @json($tenants->pluck('records_count'));
      if (recordSeries.reduce(function (a, b) { return a + b; }, 0) > 0) {
        new ApexCharts(document.querySelector('#recordsChart'), {
          chart: { type: 'donut', height: 350 },
          series: recordSeries, labels: companyLabels,
          colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
          legend: { position: 'bottom', fontSize: '13px' },
        }).render();
      } else {
        document.querySelector('#recordsChart').innerHTML = "<p class='text-center text-muted py-5'>No records yet</p>";
      }

      // Pages per Company donut
      var pageSeries = @json($tenants->pluck('pages_count'));
      if (pageSeries.reduce(function (a, b) { return a + b; }, 0) > 0) {
        new ApexCharts(document.querySelector('#pagesChart'), {
          chart: { type: 'donut', height: 350 },
          series: pageSeries, labels: companyLabels,
          colors: ['#36b9cc', '#1cc88a', '#f6c23e', '#e74a3b', '#4e73df'],
          legend: { position: 'bottom', fontSize: '13px' },
        }).render();
      } else {
        document.querySelector('#pagesChart').innerHTML = "<p class='text-center text-muted py-5'>No pages yet</p>";
      }

      // Sections by Type pie (all companies)
      var typeLabels = @json($sectionsByType->keys()->map(fn ($t) => ucfirst($t))->values());
      var typeSeries = @json($sectionsByType->values());
      if (typeSeries.length > 0) {
        new ApexCharts(document.querySelector('#typeChart'), {
          chart: { type: 'pie', height: 330 },
          series: typeSeries, labels: typeLabels,
          colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
          legend: { position: 'bottom', fontSize: '12px' },
        }).render();
      } else {
        document.querySelector('#typeChart').innerHTML = "<p class='text-center text-muted py-5'>No sections yet</p>";
      }
    });
  </script>
@endpush
