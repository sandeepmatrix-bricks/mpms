<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $role->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Scope <span class="text-danger">*</span></label>
              <select name="scope" class="form-select">
                @foreach (['platform', 'tenant'] as $scope)
                  <option value="{{ $scope }}" @selected(old('scope', $role->scope ?? 'tenant') === $scope)>{{ ucfirst($scope) }}</option>
                @endforeach
              </select>
              <small class="text-muted">Platform roles oversee all companies; tenant roles are scoped to one.</small>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Permissions</label>
            @if ($permissions->isEmpty())
              <p class="text-muted mb-0">No permissions defined yet. Create some under <a href="{{ route('admin.permissions.index') }}">Permissions</a>.</p>
            @else
              <div class="row">
                @foreach ($permissions as $permission)
                  <div class="col-md-4 col-sm-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                             id="perm-{{ $permission->id }}"
                             @checked(in_array($permission->id, old('permissions', $selected)))>
                      <label class="form-check-label" for="perm-{{ $permission->id }}"><code>{{ $permission->key }}</code></label>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Role</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
