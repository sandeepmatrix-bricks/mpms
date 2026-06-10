@php($user = $user ?? new \App\Models\User())
@php($selectedCategories = collect(old('job_categories', $membership->job_category_ids ?? []))->map(fn ($v) => (string) $v))
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 9876543210">
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                <small class="text-danger">No roles yet — create one under Roles &amp; Permissions first.</small>
              @endif
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Job Department</label>
              <select name="job_categories[]" class="form-select @error('job_categories') is-invalid @enderror" multiple size="5">
                @foreach ($jobCategories as $category)
                  <option value="{{ $category->id }}" @selected($selectedCategories->contains((string) $category->id))>{{ $category->name }}</option>
                @endforeach
              </select>
              @error('job_categories.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              @if ($jobCategories->isEmpty())
                <small class="text-muted">No departments yet — add them under Job Management → Departments.</small>
              @else
                <small class="text-muted">Hold Ctrl/Cmd to select more than one.</small>
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

          @if ($user->exists)
            <hr>
            <h6 class="mb-2">Reset password</h6>
            <div class="row">
              <div class="col-md-6 mb-2">
                <label class="form-label">New password</label>
                <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" placeholder="Leave blank to keep current">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Set a new password here if the user forgot theirs. They can log in with it straight away.</small>
              </div>
              <div class="col-md-6 mb-2 d-flex align-items-center">
                <div class="form-check mt-3">
                  <input type="hidden" name="force_change" value="0">
                  <input class="form-check-input" type="checkbox" id="force_change" name="force_change" value="1" @checked(old('force_change'))>
                  <label class="form-check-label" for="force_change">Require the user to change it on next login</label>
                </div>
              </div>
            </div>
          @endif

          @unless ($user->exists)
            <div class="form-check form-switch mb-3">
              <input type="hidden" name="auto_password" value="0">
              <input class="form-check-input" type="checkbox" role="switch" id="auto_password" name="auto_password" value="1"
                     @checked(old('auto_password', true)) onchange="document.getElementById('pwdField').style.display = this.checked ? 'none' : 'block';">
              <label class="form-check-label" for="auto_password">Auto-generate password (emailed to the user)</label>
            </div>
            <div class="mb-3" id="pwdField" style="display:{{ old('auto_password', true) ? 'none' : 'block' }};">
              <label class="form-label">Password</label>
              <input type="text" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" placeholder="Minimum 8 characters">
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          @endunless

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Save User' : 'Create User' }}</button>
            <a href="{{ route('company.users.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
