@php($questions = old('job_questions', $description->job_questions ?? []))
@php($teal = 'background:#0d6e6e;')
<div class="row">
  <div class="col-12">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
      @csrf
      @if ($method !== 'POST') @method($method) @endif

      {{-- Banner Details --}}
      <div class="card">
        <div class="card-header text-white" style="{{ $teal }}"><h5 class="mb-0">Banner Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Banner Heading</label>
              <input type="text" name="banner_heading" class="form-control" value="{{ old('banner_heading', $description->banner_heading) }}" placeholder="Step Into Growth with ...">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Banner Image</label>
              <input type="file" name="banner_image" class="form-control @error('banner_image') is-invalid @enderror" accept="image/*">
              @error('banner_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @if ($description->banner_image)
                <img src="{{ asset('uploads/careers/'.$description->banner_image) }}" alt="banner" class="img-fluid mt-2" style="max-height:80px;">
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Job Details --}}
      <div class="card">
        <div class="card-header text-white" style="{{ $teal }}"><h5 class="mb-0">Job Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Designation <span class="text-danger">*</span></label>
              <select name="job_listing_id" class="form-select @error('job_listing_id') is-invalid @enderror" required>
                <option value="">— Select a designation —</option>
                @foreach ($listings as $listing)
                  <option value="{{ $listing->id }}" @selected(old('job_listing_id', $description->job_listing_id) === $listing->id)>{{ $listing->job_role }}</option>
                @endforeach
              </select>
              @error('job_listing_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @if ($listings->isEmpty())
                <small class="text-danger">No designations yet — add one under Job Management → Designation.</small>
              @endif
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Location</label>
              <input type="text" name="location" class="form-control" value="{{ old('location', $description->location) }}" placeholder="e.g. Mumbai">
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Experience Required</label>
              <input type="text" name="experience_required" class="form-control" value="{{ old('experience_required', $description->experience_required) }}" placeholder="e.g. 15-20 years">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Working Days</label>
              <input type="text" name="working_days" class="form-control" value="{{ old('working_days', $description->working_days) }}" placeholder="e.g. Monday - Saturday">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Job Type</label>
              <input type="text" name="job_type" class="form-control" value="{{ old('job_type', $description->job_type) }}" placeholder="e.g. Full-Time">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Reporting To</label>
              <input type="text" name="reporting_to" class="form-control" value="{{ old('reporting_to', $description->reporting_to) }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Designation Label</label>
              <input type="text" name="designation" class="form-control" value="{{ old('designation', $description->designation) }}">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-1">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                  <option value="{{ $status }}" @selected(old('status', $description->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- Rich text sections --}}
      @foreach (['about_role' => 'About Job Description', 'key_responsibilities' => 'Key Responsibilities', 'key_skills_competencies' => 'Key Skills & Competencies', 'success_looks' => 'What Success Looks Like in this Role', 'qualification' => 'Qualifications'] as $field => $label)
        <div class="card">
          <div class="card-header text-white" style="{{ $teal }}"><h5 class="mb-0">{{ $label }}</h5></div>
          <div class="card-body">
            <textarea name="{{ $field }}" rows="4" class="form-control">{{ old($field, $description->$field) }}</textarea>
          </div>
        </div>
      @endforeach

      {{-- Job Profile Questions --}}
      <div class="card">
        <div class="card-header text-white" style="{{ $teal }}"><h5 class="mb-0">Job Profile Questions</h5></div>
        <div class="card-body">
          <div id="questions">
            @foreach ($questions as $i => $q)
              <div class="row g-2 mb-2 align-items-center question-row">
                <div class="col-md-5"><input type="text" name="job_questions[{{ $i }}][question]" class="form-control" placeholder="Question" value="{{ $q['question'] ?? '' }}"></div>
                <div class="col-md-2">
                  <select name="job_questions[{{ $i }}][type]" class="form-select q-type" onchange="toggleOptions(this)">
                    @foreach (['text' => 'Text', 'dropdown' => 'Dropdown', 'radio' => 'Radio'] as $val => $lbl)
                      <option value="{{ $val }}" @selected(($q['type'] ?? 'text') === $val)>{{ $lbl }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 q-options" style="display:{{ in_array($q['type'] ?? 'text', ['dropdown', 'radio'], true) ? 'block' : 'none' }};">
                  <input type="text" name="job_questions[{{ $i }}][options]" class="form-control" placeholder="Options (comma separated)" value="{{ $q['options'] ?? '' }}">
                </div>
                <div class="col-md-1">
                  <div class="form-check"><input type="checkbox" class="form-check-input" name="job_questions[{{ $i }}][required]" value="1" @checked(! empty($q['required']))><label class="form-check-label ms-1">Required</label></div>
                </div>
                <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger w-100" onclick="this.closest('.question-row').remove()">X</button></div>
              </div>
            @endforeach
          </div>
          <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addQuestion()">+ Add Question</button>
        </div>
      </div>

      <div class="text-end mb-4">
        <a href="{{ route('company.job-descriptions.index') }}" class="btn btn-danger">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Job Description</button>
      </div>
    </form>
  </div>
</div>

<script>
  let qIndex = {{ count($questions) }};
  function addQuestion() {
    const html = `<div class="row g-2 mb-2 align-items-center question-row">
      <div class="col-md-5"><input type="text" name="job_questions[${qIndex}][question]" class="form-control" placeholder="Question"></div>
      <div class="col-md-2"><select name="job_questions[${qIndex}][type]" class="form-select q-type" onchange="toggleOptions(this)"><option value="text">Text</option><option value="dropdown">Dropdown</option><option value="radio">Radio</option></select></div>
      <div class="col-md-3 q-options" style="display:none;"><input type="text" name="job_questions[${qIndex}][options]" class="form-control" placeholder="Options (comma separated)"></div>
      <div class="col-md-1"><div class="form-check"><input type="checkbox" class="form-check-input" name="job_questions[${qIndex}][required]" value="1"><label class="form-check-label ms-1">Required</label></div></div>
      <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger w-100" onclick="this.closest('.question-row').remove()">X</button></div>
    </div>`;
    document.getElementById('questions').insertAdjacentHTML('beforeend', html);
    qIndex++;
  }

  // Show the Options field only for Dropdown / Radio question types.
  function toggleOptions(select) {
    const row = select.closest('.question-row');
    const opts = row.querySelector('.q-options');
    if (!opts) return;
    opts.style.display = (select.value === 'dropdown' || select.value === 'radio') ? 'block' : 'none';
  }
</script>
