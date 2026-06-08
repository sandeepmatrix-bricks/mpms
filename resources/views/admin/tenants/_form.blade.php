<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $tenant->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                   value="{{ old('slug', $tenant->slug) }}" placeholder="auto-generated from name">
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Leave blank to generate from the name. Lowercase, dashes only.</small>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                  <option value="{{ $status }}" @selected(old('status', $tenant->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Theme</label>
              <input type="text" name="theme" class="form-control"
                     value="{{ old('theme', data_get($tenant->settings, 'branding.theme', 'default')) }}">
              <small class="text-muted">Stored under <code>settings.branding.theme</code>.</small>
            </div>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Tenant</button>
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
