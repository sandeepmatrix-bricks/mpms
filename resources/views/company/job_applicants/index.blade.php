@extends('company.layouts.master')

@php
  $isIncomplete = ($mode ?? 'complete') === 'incomplete';
  $heading = $isIncomplete ? 'Incomplete Records' : 'Job Applicants';
  $route = $isIncomplete ? route('company.applicants.incomplete') : route('company.applicants.index');
@endphp

@section('title', $heading)
@section('page-title', $heading)
@section('breadcrumbs')<li class="breadcrumb-item active">{{ $heading }}</li>@endsection

@section('content')
  {{-- Education programs list — shared by the Education filter and the Add Applicant form. --}}
  <datalist id="edu-programs-list">
    @foreach ($educationPrograms as $prog)
      <option value="{{ $prog }}"></option>
    @endforeach
  </datalist>

  <div class="row">
    <div class="col-12">
      {{-- Filter card --}}
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="fa fa-search me-2"></i>Filter Applications</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ $route }}">
            @csrf
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
                <input type="text" name="location" id="filter-location" class="form-control" value="{{ $filters['location'] ?? '' }}"
                       placeholder="Search location…" autocomplete="off">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Job Type</label>
                <input type="text" name="job_type" class="form-control" value="{{ $filters['job_type'] ?? '' }}" placeholder="Select Job Type">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Education</label>
                <input type="text" name="education" class="form-control" list="edu-programs-list" autocomplete="off"
                       value="{{ $filters['education'] ?? '' }}" placeholder="Search program / degree…">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Answered Questions</label>
                <select name="answered" class="form-select">
                  <option value="">Select</option>
                  <option value="yes" @selected(($filters['answered'] ?? '') === 'yes')>Yes</option>
                  <option value="no" @selected(($filters['answered'] ?? '') === 'no')>No</option>
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
          <div>
            <h5 class="mb-0">{{ $heading }}</h5>
            <small class="text-muted">{{ $isIncomplete ? 'Applications candidates started but never submitted.' : 'Completed applications submitted to your roles.' }}</small>
          </div>
          @unless ($isIncomplete)
            @permission('applicants.write')
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addApplicantModal">
                <i class="fa fa-plus me-1"></i> Add Applicant
              </button>
            @endpermission
          @endunless
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

  {{-- Add Applicant modal (HR manual entry) --}}
  @unless ($isIncomplete)
    @permission('applicants.write')
      <div class="modal fade" id="addApplicantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content" style="border:0;border-radius:14px;">
            <form method="POST" action="{{ route('company.applicants.store') }}" enctype="multipart/form-data">
              @csrf
              <div class="modal-header text-white" style="background:#0d6e6e;border:0;">
                <h5 class="modal-title"><i class="fa fa-user-plus me-2"></i>Add Applicant</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select id="modal-department" class="form-select" required>
                      <option value="">Select Department</option>
                      @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Job Title (Designation) <span class="text-danger">*</span></label>
                    <select name="job_listing_id" id="modal-job-title" class="form-select @error('job_listing_id') is-invalid @enderror" required>
                      <option value="">Select Job Title</option>
                      @foreach ($listings as $listing)
                        <option value="{{ $listing->id }}" data-department="{{ $listing->job_category_id }}">{{ $listing->job_role }}</option>
                      @endforeach
                    </select>
                    @error('job_listing_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                      <option value="">Select</option>
                      @foreach (['Male', 'Female', 'Other'] as $g)
                        <option value="{{ $g }}" @selected(old('gender') === $g)>{{ $g }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Address (street / building / flat)</label>
                    <input type="text" name="address_line" class="form-control" value="{{ old('address_line') }}" placeholder="e.g. Greenfield Apartment, Building 12, Flat 15">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Location</label>
                    <input type="text" name="location_name" id="modal-location" class="form-control" autocomplete="off"
                           value="{{ old('location_name') }}" placeholder="Search location…">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" id="modal-city" class="form-control" value="{{ old('city') }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" id="modal-state" class="form-control" value="{{ old('state') }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" id="modal-pincode" class="form-control" value="{{ old('pincode') }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" id="modal-country" class="form-control" value="{{ old('country', 'India') }}">
                  </div>

                  @php
                    $sources = [
                      'Walk in', 'Referral', $company->name.' Website - Career Page', 'Recruitment Agency',
                      'University Recruiting', 'Naukri', 'Indeed', 'LinkedIn', 'Facebook',
                      'Other Job Board', 'Other Social Media', 'Other',
                    ];
                  @endphp
                  <div class="col-md-4">
                    <label class="form-label">Source</label>
                    <select name="source" class="form-select">
                      <option value="">Select One…</option>
                      @foreach ($sources as $src)
                        <option value="{{ $src }}" @selected(old('source') === $src)>{{ $src }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Position / Applied For</label>
                    <select name="position" id="modal-position" class="form-select">
                      <option value="">Defaults to job title</option>
                      @foreach ($listings as $listing)
                        <option value="{{ $listing->job_role }}" data-department="{{ $listing->job_category_id }}" @selected(old('position') === $listing->job_role)>{{ $listing->job_role }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                      @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', 'new') === $key)>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Resume</label>
                    <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Portfolio URL</label>
                    <input type="url" name="portfolio_url" class="form-control" value="{{ old('portfolio_url') }}" placeholder="https://…">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cover Letter / Notes</label>
                    <textarea name="cover_letter" class="form-control" rows="3">{{ old('cover_letter') }}</textarea>
                  </div>

                  {{-- Education (repeatable) --}}
                  <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center section-title">
                      <span><i class="fa fa-graduation-cap me-2"></i>Education</span>
                      <button type="button" class="btn btn-sm btn-primary" id="add-education"><i class="fa fa-plus me-1"></i>Add</button>
                    </div>
                    <div id="education-rows" class="mt-2">
                      @foreach (old('education', [[]]) as $i => $row)
                        <div class="repeat-row row g-2 mb-2">
                          <div class="col-md-4"><input name="education[{{ $i }}][school]" class="form-control form-control-sm" placeholder="School / University" value="{{ $row['school'] ?? '' }}"></div>
                          <div class="col-md-3"><input name="education[{{ $i }}][program]" class="form-control form-control-sm" placeholder="Program / Degree" list="edu-programs-list" autocomplete="off" value="{{ $row['program'] ?? '' }}"></div>
                          <div class="col-md-2"><input type="date" name="education[{{ $i }}][startDate]" class="form-control form-control-sm" title="Start date" value="{{ $row['startDate'] ?? '' }}"></div>
                          <div class="col-md-2"><input type="date" name="education[{{ $i }}][endDate]" class="form-control form-control-sm" title="End date" value="{{ $row['endDate'] ?? '' }}"></div>
                          <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">&times;</button></div>
                        </div>
                      @endforeach
                    </div>
                  </div>

                  {{-- Work Experience (repeatable) --}}
                  <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center section-title">
                      <span><i class="fa fa-briefcase me-2"></i>Work Experience</span>
                      <button type="button" class="btn btn-sm btn-primary" id="add-experience"><i class="fa fa-plus me-1"></i>Add</button>
                    </div>
                    <div id="experience-rows" class="mt-2">
                      @foreach (old('work_experience', [[]]) as $i => $row)
                        <div class="repeat-row row g-2 mb-2">
                          <div class="col-md-4"><input name="work_experience[{{ $i }}][company]" class="form-control form-control-sm" placeholder="Company" value="{{ $row['company'] ?? '' }}"></div>
                          <div class="col-md-3"><input name="work_experience[{{ $i }}][position]" class="form-control form-control-sm" placeholder="Position" value="{{ $row['position'] ?? '' }}"></div>
                          <div class="col-md-2"><input type="date" name="work_experience[{{ $i }}][startDate]" class="form-control form-control-sm" title="Start date" value="{{ $row['startDate'] ?? '' }}"></div>
                          <div class="col-md-2"><input type="date" name="work_experience[{{ $i }}][endDate]" class="form-control form-control-sm" title="End date" value="{{ $row['endDate'] ?? '' }}"></div>
                          <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">&times;</button></div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i>Save Applicant</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endpermission
  @endunless

  @permission('applicants.write')
  @push('page-styles')
  <style>
    #addApplicantModal .modal-body { background:#fff; max-height:72vh; overflow-y:auto; }
    #addApplicantModal .modal-dialog { max-height:92vh; }
    #addApplicantModal .form-label { color:#334155; font-weight:600; font-size:.85rem; margin-bottom:.25rem; }
    #addApplicantModal .form-control, #addApplicantModal .form-select { color:#212529; }
    #addApplicantModal .section-title { font-weight:700; color:#0d6e6e; font-size:.9rem; border-top:1px solid #eef2f7; padding-top:.85rem; margin-top:.4rem; }
  </style>
  @endpush

  @push('page-scripts')
  <script>
  (function () {
    var dept = document.getElementById('modal-department');
    // Both the Job Title and the Position dropdown list designations of the
    // chosen department, so filter them together when the department changes.
    var deptDriven = [document.getElementById('modal-job-title'), document.getElementById('modal-position')].filter(Boolean);
    if (dept && deptDriven.length) {
      var filter = function () {
        var sel = dept.value;
        deptDriven.forEach(function (selectEl) {
          Array.from(selectEl.options).forEach(function (opt) {
            if (!opt.value) return;
            var match = !sel || opt.dataset.department === sel;
            opt.hidden = !match;
            if (!match && opt.selected) { opt.selected = false; selectEl.value = ''; }
          });
        });
      };
      dept.addEventListener('change', filter);
      filter();
    }

    // Repeatable Education / Work Experience rows.
    function addRow(containerId, prefix, fields) {
      var box = document.getElementById(containerId);
      if (!box) return;
      var i = box.querySelectorAll('.repeat-row').length;
      var cols = fields.map(function (f) {
        var type = f.type || 'text';
        var ph = f.ph ? ' placeholder="' + f.ph + '"' : (f.title ? ' title="' + f.title + '"' : '');
        var list = f.list ? ' list="' + f.list + '" autocomplete="off"' : '';
        return '<div class="col-md-' + f.col + '"><input type="' + type + '" name="' + prefix + '[' + i + '][' + f.key + ']" class="form-control form-control-sm"' + ph + list + '></div>';
      }).join('');
      var row = document.createElement('div');
      row.className = 'repeat-row row g-2 mb-2';
      row.innerHTML = cols + '<div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove">&times;</button></div>';
      box.appendChild(row);
    }

    var addEdu = document.getElementById('add-education');
    if (addEdu) addEdu.addEventListener('click', function () {
      addRow('education-rows', 'education', [
        {col:4, key:'school', ph:'School / University'},
        {col:3, key:'program', ph:'Program / Degree', list:'edu-programs-list'},
        {col:2, key:'startDate', type:'date', title:'Start date'},
        {col:2, key:'endDate', type:'date', title:'End date'},
      ]);
    });

    var addExp = document.getElementById('add-experience');
    if (addExp) addExp.addEventListener('click', function () {
      addRow('experience-rows', 'work_experience', [
        {col:4, key:'company', ph:'Company'},
        {col:3, key:'position', ph:'Position'},
        {col:2, key:'startDate', type:'date', title:'Start date'},
        {col:2, key:'endDate', type:'date', title:'End date'},
      ]);
    });

    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.remove-row');
      if (!btn) return;
      var rows = btn.closest('[id$="-rows"]');
      // Keep at least one row in each section.
      if (rows && rows.querySelectorAll('.repeat-row').length > 1) {
        btn.closest('.repeat-row').remove();
      } else {
        btn.closest('.repeat-row').querySelectorAll('input').forEach(function (inp) { inp.value = ''; });
      }
    });

    // Re-open the modal automatically if validation failed on submit.
    @if ($errors->any())
      var el = document.getElementById('addApplicantModal');
      if (el && window.bootstrap) new bootstrap.Modal(el).show();
    @endif
  })();
  </script>
  @endpush
  @endpermission

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

  // Location autocomplete — office names from the pincode list.
  // Uses a RELATIVE url (avoids cross-origin when APP_URL host differs from the
  // browser host) and a body-fixed dropdown (so nothing can clip it).
  function setupLocation(input, targets) {
    if (!input) return;
    targets = targets || {};
    var url = '/company/applicants-locations';
    var timer = null;

    var box = document.createElement('div');
    box.className = 'list-group shadow';
    box.style.cssText = 'position:fixed; z-index:3000; max-height:240px; overflow:auto; display:none;';
    document.body.appendChild(box);

    function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;'); }
    function hide() { box.style.display = 'none'; }
    function place() {
      var r = input.getBoundingClientRect();
      box.style.top = r.bottom + 'px'; box.style.left = r.left + 'px'; box.style.width = r.width + 'px';
    }

    function render(rows) {
      if (!rows || !rows.length) { box.innerHTML = ''; hide(); return; }
      box.innerHTML = rows.map(function (r) {
        var sub = [r.district, r.state, r.pincode].filter(Boolean).join(', ');
        return '<button type="button" class="list-group-item list-group-item-action py-1"' +
          ' data-name="' + esc(r.name) + '" data-district="' + esc(r.district) + '" data-state="' + esc(r.state) + '" data-pincode="' + esc(r.pincode) + '">' +
          '<div>' + esc(r.name) + '</div><small class="text-muted">' + esc(sub) + '</small></button>';
      }).join('');
      place(); box.style.display = 'block';
    }

    input.addEventListener('input', function () {
      var q = input.value.trim();
      clearTimeout(timer);
      if (q.length < 2) { hide(); return; }
      timer = setTimeout(function () {
        fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
          .then(function (r) { return r.json(); })
          .then(render)
          .catch(hide);
      }, 250);
    });

    box.addEventListener('mousedown', function (e) {
      var b = e.target.closest('[data-name]');
      if (!b) return;
      e.preventDefault();
      input.value = b.getAttribute('data-name');
      // Auto-fill the structured address fields from the chosen location.
      if (targets.city) targets.city.value = b.getAttribute('data-district') || '';
      if (targets.state) targets.state.value = b.getAttribute('data-state') || '';
      if (targets.pincode) targets.pincode.value = b.getAttribute('data-pincode') || '';
      if (targets.country && !targets.country.value) targets.country.value = 'India';
      hide();
    });

    window.addEventListener('resize', function () { if (box.style.display === 'block') place(); });
    window.addEventListener('scroll', function () { if (box.style.display === 'block') place(); }, true);
    document.addEventListener('click', function (e) { if (e.target !== input && !box.contains(e.target)) hide(); });
  }

  setupLocation(document.getElementById('filter-location'), null);
  setupLocation(document.getElementById('modal-location'), {
    city: document.getElementById('modal-city'),
    state: document.getElementById('modal-state'),
    pincode: document.getElementById('modal-pincode'),
    country: document.getElementById('modal-country'),
  });
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
