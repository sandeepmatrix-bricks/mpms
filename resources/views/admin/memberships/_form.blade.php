<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">User <span class="text-danger">*</span></label>
            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
              <option value="">— Select user —</option>
              @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected(old('user_id', $membership->user_id) === $u->id)>{{ $u->name }} ({{ $u->email }})</option>
              @endforeach
            </select>
            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Scope (Tenant)</label>
              <select name="tenant_id" class="form-select">
                <option value="" @selected(old('tenant_id', $membership->tenant_id) === null)>— Platform (all tenants) —</option>
                @foreach ($tenants as $t)
                  <option value="{{ $t->id }}" @selected(old('tenant_id', $membership->tenant_id) === $t->id)>{{ $t->name }}</option>
                @endforeach
              </select>
              <small class="text-muted">Leave on “Platform” for a parent admin who oversees every company.</small>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">— Select role —</option>
                @foreach ($roles as $r)
                  <option value="{{ $r->id }}" @selected(old('role_id', $membership->role_id) === $r->id)>{{ $r->name }} ({{ $r->scope }})</option>
                @endforeach
              </select>
              @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Membership</button>
            <a href="{{ route('admin.memberships.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
