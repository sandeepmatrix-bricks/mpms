<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">Key <span class="text-danger">*</span></label>
            <input type="text" name="key" class="form-control @error('key') is-invalid @enderror"
                   value="{{ old('key', $permission->key) }}" placeholder="manage_pages" required>
            @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Normalized to <code>snake_case</code> on save.</small>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Permission</button>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
