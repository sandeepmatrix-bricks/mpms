@php($dataValue = old('data', $record->data ? json_encode($record->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''))
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">Collection <span class="text-danger">*</span></label>
            <select name="collection_id" class="form-select @error('collection_id') is-invalid @enderror" required>
              <option value="">— Select collection —</option>
              @foreach ($collections as $c)
                <option value="{{ $c->id }}" @selected(old('collection_id', $record->collection_id) === $c->id)>{{ $c->tenant?->name }} · {{ $c->name }}</option>
              @endforeach
            </select>
            @error('collection_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">The tenant is set automatically from the collection.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Data (JSON)</label>
            <textarea name="data" rows="8" class="form-control font-monospace @error('data') is-invalid @enderror"
                      placeholder='{"name": "Blue Widget", "price": 29.99}'>{{ $dataValue }}</textarea>
            @error('data')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Record</button>
            <a href="{{ route('admin.records.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
