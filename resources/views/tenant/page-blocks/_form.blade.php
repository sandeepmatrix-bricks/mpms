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
                <option value="">&mdash; Select page &mdash;</option>
                @foreach ($pages as $p)
                  <option value="{{ $p->id }}" @selected(old('page_id', $block->page_id) === $p->id)>{{ $p->title }}</option>
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

          <div class="mb-3">
            <label class="form-label">Content (rich text)</label>
            <textarea name="content" class="summernote @error('content') is-invalid @enderror">{{ old('content', $block->content) }}</textarea>
            @error('content')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <small class="text-muted">Optional HTML body for text-style sections.</small>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Block</button>
            <a href="{{ route('tenant.page-blocks.index', $tenant) }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('tenant-styles')
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('tenant-scripts')
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery && jQuery.fn.summernote) {
        jQuery('.summernote').summernote({
          height: 220,
          toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['codeview']],
          ],
        });
      }
    });
  </script>
@endpush
