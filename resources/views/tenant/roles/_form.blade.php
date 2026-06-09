@php($selected = collect(old('permissions', $selected ?? []))->map(fn ($v) => (string) $v))
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">Role name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $role->name) }}" placeholder="e.g. Manager, Employee" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <label class="form-label">Permissions</label>
          <p class="text-muted mb-2" style="font-size:13px;">Tick what this role can access. Unticked permissions hide the matching tabs and block the routes.</p>
          <div class="row">
            @foreach ($permissions as $permission)
              <div class="col-md-4 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="permissions[]"
                         id="perm_{{ $permission->id }}" value="{{ $permission->id }}"
                         @checked($selected->contains((string) $permission->id))>
                  <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->key }}</label>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Role</button>
            <a href="{{ route('tenant.roles.index', $tenant) }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
