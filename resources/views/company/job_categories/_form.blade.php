<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Department name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $category->name) }}" placeholder="e.g. Sales, Human Resource, Marketing" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                  <option value="{{ $status }}" @selected(old('status', $category->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Department</button>
            <a href="{{ route('company.job-categories.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
