@extends('tenant.layouts.master')

@section('title', $tenant->name.' Dashboard')
@section('page-title', $tenant->name)
@section('breadcrumbs')<li class="breadcrumb-item active">Dashboard</li>@endsection

@push('tenant-styles')
  <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.css" rel="stylesheet">
  <style>
    .dashboard-title { font-weight:700; color:#1e293b; margin-bottom:0; font-size:1.35rem; letter-spacing:-.3px; }
    .stat-card { border:none; border-radius:1rem; transition:transform .25s ease, box-shadow .25s ease; }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.09)!important; }
    .chart-card { border:none; border-radius:1rem; box-shadow:0 2px 12px rgba(0,0,0,.06); }
    .section-heading { font-weight:700; font-size:.88rem; color:#475569; letter-spacing:.3px; text-align:center; margin-bottom:1rem; }
    .fw-800 { font-weight:800; }
    #contentChart, #typeChart { min-height:330px; }
    #collectionChart, #pageChart { min-height:350px; }
  </style>
@endpush

@php
  $cards = [
    ['emoji' => '📄', 'label' => 'Total Pages',       'value' => $stats['pages'],       'tone' => 'primary'],
    ['emoji' => '🧩', 'label' => 'Total Sections',    'value' => $stats['sections'],    'tone' => 'success'],
    ['emoji' => '🗂️', 'label' => 'Total Collections', 'value' => $stats['collections'], 'tone' => 'danger'],
    ['emoji' => '🗃️', 'label' => 'Total Records',     'value' => $stats['records'],     'tone' => 'warning'],
  ];
@endphp

@section('content')
  <div class="page-title d-flex align-items-center justify-content-between mb-4">
    <h3 class="dashboard-title">📊 Dashboard Overview</h3>
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
          <p class="section-heading">Content Overview</p>
          <div id="contentChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card">
        <div class="card-body">
          <p class="section-heading">Sections by Type</p>
          <div id="typeChart"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts Row 2 --}}
  <div class="row g-4 mt-2 mb-4">
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Records per Collection</p>
          <div id="collectionChart"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card chart-card h-100">
        <div class="card-body">
          <p class="section-heading">Sections per Page</p>
          <div id="pageChart"></div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('tenant-scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.3.0/apexcharts.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof ApexCharts === 'undefined') return;

      // Content Overview bar
      new ApexCharts(document.querySelector('#contentChart'), {
        chart: { type: 'bar', height: 330, toolbar: { show: false } },
        series: [{ name: 'Total', data: [{{ $stats['pages'] }}, {{ $stats['sections'] }}, {{ $stats['collections'] }}, {{ $stats['records'] }}] }],
        xaxis: { categories: ['Pages', 'Sections', 'Collections', 'Records'] },
        plotOptions: { bar: { distributed: true, borderRadius: 8, columnWidth: '45%' } },
        colors: ['#4e73df', '#1cc88a', '#e74a3b', '#f6c23e'],
        dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
        legend: { show: false },
        grid: { borderColor: '#f1f5f9' },
      }).render();

      // Sections by Type pie
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

      // Records per Collection donut
      var colLabels = @json(collect($recordsPerCollection)->pluck('name'));
      var colSeries = @json(collect($recordsPerCollection)->pluck('total'));
      if (colSeries.length > 0 && colSeries.reduce(function (a, b) { return a + b; }, 0) > 0) {
        new ApexCharts(document.querySelector('#collectionChart'), {
          chart: { type: 'donut', height: 350 },
          series: colSeries, labels: colLabels,
          colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
          legend: { position: 'bottom', fontSize: '13px' },
        }).render();
      } else {
        document.querySelector('#collectionChart').innerHTML = "<p class='text-center text-muted py-5'>No records yet</p>";
      }

      // Sections per Page donut
      var pageLabels = @json(collect($sectionsPerPage)->pluck('name'));
      var pageSeries = @json(collect($sectionsPerPage)->pluck('total'));
      if (pageSeries.length > 0) {
        new ApexCharts(document.querySelector('#pageChart'), {
          chart: { type: 'donut', height: 350 },
          series: pageSeries, labels: pageLabels,
          colors: ['#36b9cc', '#1cc88a', '#f6c23e', '#e74a3b', '#4e73df'],
          legend: { position: 'bottom', fontSize: '13px' },
        }).render();
      } else {
        document.querySelector('#pageChart').innerHTML = "<p class='text-center text-muted py-5'>No sections yet</p>";
      }
    });
  </script>
@endpush
