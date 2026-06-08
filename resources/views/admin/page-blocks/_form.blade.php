@php($configValue = old('config', $block->config ? json_encode($block->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''))
@php($dataSourceValue = old('data_source', $block->data_source ? json_encode($block->data_source, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''))
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Page <span class="text-danger">*</span></label>
              <select name="page_id" class="form-select @error('page_id') is-invalid @enderror" required>
                <option value="">— Select page —</option>
                @foreach ($pages as $p)
                  <option value="{{ $p->id }}" @selected(old('page_id', $block->page_id) === $p->id)>{{ $p->tenant?->name }} · {{ $p->title }}</option>
                @endforeach
              </select>
              @error('page_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Type <span class="text-danger">*</span></label>
              <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                @foreach ($types as $type)
                  <option value="{{ $type }}" @selected(old('type', $block->type) === $type)>{{ ucfirst($type) }}</option>
                @endforeach
              </select>
              @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Region <span class="text-danger">*</span></label>
              <input type="text" name="region" class="form-control @error('region') is-invalid @enderror"
                     value="{{ old('region', $block->region ?? 'body') }}" required>
              @error('region')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Position <span class="text-danger">*</span></label>
              <input type="number" name="position" class="form-control @error('position') is-invalid @enderror"
                     value="{{ old('position', $block->position ?? 0) }}" min="0" required>
              @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Config (JSON)</label>
              <textarea name="config" rows="6" class="form-control font-monospace @error('config') is-invalid @enderror"
                        placeholder='{"label": "Revenue", "value": "$64K"}'>{{ $configValue }}</textarea>
              @error('config')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Data source (JSON)</label>
              <textarea name="data_source" rows="6" class="form-control font-monospace @error('data_source') is-invalid @enderror"
                        placeholder='{"collection": "products"}'>{{ $dataSourceValue }}</textarea>
              @error('data_source')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Block</button>
            <a href="{{ route('admin.page-blocks.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
