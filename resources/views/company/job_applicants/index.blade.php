@extends('company.layouts.master')

@php($isIncomplete = ($mode ?? 'complete') === 'incomplete')
@php($heading = $isIncomplete ? 'Incomplete Records' : 'Job Applicants')
@php($route = $isIncomplete ? route('company.applicants.incomplete') : route('company.applicants.index'))

@section('title', $heading)
@section('page-title', $heading)
@section('breadcrumbs')<li class="breadcrumb-item active">{{ $heading }}</li>@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      {{-- Filter card --}}
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="fa fa-search me-2"></i>Filter Applications</h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ $route }}">
            <div class="row">
              <div class="col-md-3 mb-3">
                <label class="form-label">Department</label>
                <select name="department" id="filter-department" class="form-select">
                  <option value="">Select Department</option>
                  @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" @selected(($filters['department'] ?? '') === $dept->id)>{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Job Title</label>
                <select name="job_title" id="filter-job-title" class="form-select">
                  <option value="">Select Job Title</option>
                  @foreach ($listings as $listing)
                    <option value="{{ $listing->id }}" data-department="{{ $listing->job_category_id }}"
                            @selected(($filters['job_title'] ?? '') === $listing->id)>{{ $listing->job_role }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="{{ $filters['location'] ?? '' }}" placeholder="Select Location">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Job Type</label>
                <input type="text" name="job_type" class="form-control" value="{{ $filters['job_type'] ?? '' }}" placeholder="Select Job Type">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Education</label>
                <input type="text" name="education" class="form-control" placeholder="Select Education">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Answered Questions</label>
                <select name="answered" class="form-select">
                  <option value="">Select</option>
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                  <option value="">Select Gender</option>
                  @foreach (['Male', 'Female', 'Other'] as $g)
                    <option value="{{ $g }}" @selected(($filters['gender'] ?? '') === $g)>{{ $g }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Work Exp (Years)</label>
                <input type="text" name="work_exp" class="form-control" placeholder="-- Select --">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ $filters['from_date'] ?? '' }}">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ $filters['to_date'] ?? '' }}">
              </div>
              <div class="col-md-6 mb-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ $route }}" class="btn btn-secondary"><i class="fa fa-refresh me-1"></i>Reset Filters</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Applicants table --}}
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ $heading }}</h5>
          <small class="text-muted">{{ $isIncomplete ? 'Applications candidates started but never submitted.' : 'Completed applications submitted to your roles.' }}</small>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="careerTable" class="table table-hover align-middle" style="width:100%;">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Phone</th>
                  <th>Applied</th>
                  <th>Resume</th>
                  <th>Status</th>
                  <th class="text-end">Profile</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($applicants as $applicant)
                  <tr>
                    <td class="f-w-600">{{ $applicant->name ?? '—' }}</td>
                    <td>{{ $applicant->listing?->job_role ?? $applicant->position ?? '—' }}</td>
                    <td>{{ $applicant->phone ?? '—' }}</td>
                    <td data-order="{{ $applicant->created_at?->timestamp ?? 0 }}">{{ $applicant->created_at?->format('d M, Y h:i A') ?? '—' }}</td>
                    <td>
                      @if ($applicant->resume)
                        <a href="{{ asset('resumes/'.$applicant->resume) }}" target="_blank" class="btn btn-sm btn-outline-primary">Resume</a>
                      @else
                        <span class="text-muted">—</span>
                      @endif
                    </td>
                    <td data-order="{{ $applicant->status }}" style="white-space:nowrap;">
                      @permission('applicants.edit')
                        <form method="POST" action="{{ route('company.applicants.status', $applicant) }}" class="m-0">
                          @csrf @method('PUT')
                          <select name="status" class="form-select form-select-sm status-pill"
                                  style="width:160px; border-radius:50rem; padding-left:14px; background-color: {{ $statusColors[$applicant->status] ?? '#6c757d' }}; color:#fff; font-weight:600; border:none;"
                                  onchange="recolorStatus(this); this.form.submit()">
                            @foreach ($statuses as $key => $label)
                              <option value="{{ $key }}" @selected($applicant->status === $key) style="background:#fff;color:#000;">{{ $label }}</option>
                            @endforeach
                          </select>
                        </form>
                      @else
                        <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $statusColors[$applicant->status] ?? '#6c757d' }}; color:#fff;">{{ $statuses[$applicant->status] ?? ucfirst($applicant->status) }}</span>
                      @endpermission
                    </td>
                    <td class="text-end ps-3" style="white-space:nowrap;">
                      <a href="{{ route('company.applicants.show', $applicant) }}" class="btn btn-sm btn-primary rounded-pill px-3">Profile</a>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-center text-muted py-4">No {{ $isIncomplete ? 'incomplete records' : 'applicants' }} yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('page-scripts')
  <script>
  (function () {
    const dept = document.getElementById('filter-department');
    const job = document.getElementById('filter-job-title');
    if (!dept || !job) return;

    function filterJobTitles() {
      const selected = dept.value;
      Array.from(job.options).forEach(function (opt) {
        if (!opt.value) return; // keep placeholder
        const match = !selected || opt.dataset.department === selected;
        opt.hidden = !match;
        if (!match && opt.selected) { opt.selected = false; job.value = ''; }
      });
    }

    dept.addEventListener('change', filterJobTitles);
    filterJobTitles();
  })();
  </script>
  @endpush

  @push('page-styles')
    <link rel="stylesheet" href="{{ asset('admin-assets/css/vendors/datatables.css') }}">
  @endpush

  @push('page-scripts')
  <script>
    // Recolor a status <select> to match the chosen status.
    var STATUS_COLORS = @json($statusColors);
    function recolorStatus(sel) {
      var c = STATUS_COLORS[sel.value] || '#6c757d';
      sel.style.setProperty('background-color', c, 'important');
      sel.style.setProperty('color', '#fff', 'important');
    }

    jQuery(function ($) {
      if (!$.fn || !$.fn.DataTable) return;
      var $t = $('#careerTable');
      if (!$t.length) return;
      // Guard against any prior/auto init producing duplicate controls.
      if ($.fn.DataTable.isDataTable('#careerTable')) {
        $t.DataTable().destroy();
        $t.closest('.dataTables_wrapper').find('.dataTables_length, .dataTables_filter').remove();
      }
      $t.DataTable({
        destroy: true,
        order: [[3, 'desc']],            // Applied, newest first
        columnDefs: [
          { orderable: false, searchable: false, targets: [4, 5, 6] }, // Resume, Status, Profile
        ],
        language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries' },
      });
    });
  </script>
  @endpush
@endsection
