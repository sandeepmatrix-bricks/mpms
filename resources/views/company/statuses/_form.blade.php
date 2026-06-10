<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Status label <span class="text-danger">*</span></label>
              <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                     value="{{ old('label', $status->label) }}" placeholder="e.g. Offer Sent" required>
              @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Color <span class="text-danger">*</span></label>
              <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                     value="{{ old('color', $status->color ?? '#0d6e6e') }}" style="width:100%;height:38px;">
              @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Sort order</label>
              <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $status->sort_order ?? 100) }}" min="0">
            </div>
          </div>

          <div class="form-check form-switch mb-3">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $status->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active (shown in the dropdown)</label>
          </div>

          <div class="mt-2">
            <button type="submit" class="btn btn-primary">Save Status</button>
            <a href="{{ route('company.statuses.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
