@php($user = $user ?? new \App\Models\User())
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
                     value="{{ old('name', $user->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', $user->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">— Select a role —</option>
                @foreach ($roles as $role)
                  <option value="{{ $role->id }}" @selected(old('role_id', $membership->role_id ?? '') === $role->id)>{{ $role->name }}</option>
                @endforeach
              </select>
              @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @if ($roles->isEmpty())
                <small class="text-danger">No roles defined yet — create one under Roles first.</small>
              @endif
            </div>

            @if ($user->exists)
              <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $user->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
              </div>
            @endif
          </div>

          @unless ($user->exists)
            <div class="alert alert-light border d-flex align-items-center gap-2">
              <i data-feather="info" style="width:18px;"></i>
              <span>A temporary password is generated automatically and emailed to the user. They'll set their own password on first login.</span>
            </div>
          @endunless

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Save User' : 'Create User' }}</button>
            <a href="{{ route('tenant.users.index', $tenant) }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
