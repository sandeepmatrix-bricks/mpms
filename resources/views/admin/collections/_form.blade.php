@php($schemaValue = old('schema', $collection->schema ? json_encode($collection->schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''))
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Tenant <span class="text-danger">*</span></label>
              <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                <option value="">— Select tenant —</option>
                @foreach ($tenants as $t)
                  <option value="{{ $t->id }}" @selected(old('tenant_id', $collection->tenant_id) === $t->id)>{{ $t->name }}</option>
                @endforeach
              </select>
              @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $collection->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Key</label>
              <input type="text" name="key" class="form-control @error('key') is-invalid @enderror"
                     value="{{ old('key', $collection->key) }}" placeholder="auto from name (snake_case)">
              @error('key')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Schema (JSON)</label>
            <textarea name="schema" rows="8" class="form-control font-monospace @error('schema') is-invalid @enderror"
                      placeholder='{"fields": [{"name": "title", "type": "string"}]}'>{{ $schemaValue }}</textarea>
            @error('schema')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Defines the shape of records in this collection.</small>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Collection</button>
            <a href="{{ route('admin.collections.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
