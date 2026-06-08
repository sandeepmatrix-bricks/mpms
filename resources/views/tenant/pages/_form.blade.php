@php($configValue = old('config', $page->config ? json_encode($page->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ''))
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                     value="{{ old('title', $page->title) }}" required>
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Slug</label>
              <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                     value="{{ old('slug', $page->slug) }}" placeholder="auto-generated from title">
              @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Parent page</label>
              <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                <option value="">&mdash; None (top level) &mdash;</option>
                @foreach ($parents as $p)
                  <option value="{{ $p->id }}" @selected(old('parent_id', $page->parent_id) === $p->id)>{{ $p->title }}</option>
                @endforeach
              </select>
              @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Icon</label>
              <input type="text" name="icon" class="form-control" value="{{ old('icon', $page->icon) }}" placeholder="mdi-view-dashboard">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Layout <span class="text-danger">*</span></label>
              <input type="text" name="layout" class="form-control @error('layout') is-invalid @enderror"
                     value="{{ old('layout', $page->layout ?? 'single') }}" required>
              @error('layout')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">Sort order <span class="text-danger">*</span></label>
              <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                     value="{{ old('sort_order', $page->sort_order ?? 0) }}" min="0" required>
              @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 mb-3 d-flex align-items-end">
              <div class="form-check form-switch mb-2">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                       @checked(old('is_active', $page->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Config (JSON)</label>
            <textarea name="config" rows="5" class="form-control font-monospace @error('config') is-invalid @enderror"
                      placeholder='{"hero": true}'>{{ $configValue }}</textarea>
            @error('config')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Page</button>
            <a href="{{ route('tenant.pages.index', $tenant) }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
