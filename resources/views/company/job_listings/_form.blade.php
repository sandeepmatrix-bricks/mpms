<div class="row">
  <div class="col-12">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
      @csrf
      @if ($method !== 'POST') @method($method) @endif

      {{-- Banner Details --}}
      <div class="card">
        <div class="card-header text-white" style="background:#0d6e6e;">
          <h5 class="mb-0">Banner Details</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Banner Heading</label>
              <input type="text" name="banner_heading" class="form-control"
                     value="{{ old('banner_heading', $listing->banner_heading) }}" placeholder="Step Into Growth with ...">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Banner Image</label>
              <input type="file" name="banner_image" class="form-control @error('banner_image') is-invalid @enderror" accept="image/*">
              @error('banner_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <small class="text-muted d-block">Note: Max 2MB | Formats: JPG, JPEG, PNG, WEBP</small>
              @if ($listing->banner_image)
                <img src="{{ asset('uploads/careers/'.$listing->banner_image) }}" alt="banner" class="img-fluid mt-2" style="max-height:90px;">
              @endif
            </div>
          </div>
          <div class="mb-1">
            <label class="form-label">Section Heading</label>
            <textarea name="section_heading" rows="3" class="form-control" placeholder="Enter Section Heading">{{ old('section_heading', $listing->section_heading) }}</textarea>
          </div>
        </div>
      </div>

      {{-- Job Details --}}
      <div class="card">
        <div class="card-header text-white" style="background:#0d6e6e;">
          <h5 class="mb-0">Job Details</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Department <span class="text-danger">*</span></label>
              <select name="job_category_id" class="form-select @error('job_category_id') is-invalid @enderror" required>
                <option value="">— Select a department —</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}" @selected(old('job_category_id', $listing->job_category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
              </select>
              @error('job_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @if ($categories->isEmpty())
                <small class="text-danger">No departments yet — add one under Job Management → Departments.</small>
              @endif
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Job role / Designation <span class="text-danger">*</span></label>
              <input type="text" name="job_role" class="form-control @error('job_role') is-invalid @enderror"
                     value="{{ old('job_role', $listing->job_role) }}" placeholder="e.g. Global Export Manager" required>
              @error('job_role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Location <span class="text-danger">*</span></label>
              <input type="text" name="location" class="form-control" value="{{ old('location', $listing->location) }}" placeholder="e.g. Mumbai">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                  <option value="{{ $status }}" @selected(old('status', $listing->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="text-end mb-4">
        <a href="{{ route('company.job-listings.index') }}" class="btn btn-danger">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Designation</button>
      </div>
    </form>
  </div>
</div>
