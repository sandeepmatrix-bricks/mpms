<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">Company <span class="text-danger">*</span></label>
            <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
              <option value="">— Select a company —</option>
              @foreach ($companies as $company)
                <option value="{{ $company->id }}" @selected(old('tenant_id', $membership->tenant_id ?? '') === $company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
            @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($companies->isEmpty())
              <small class="text-danger">No companies available — every active company already has a user. Register a new company, or add more team members from inside the company's own login.</small>
            @else
              <small class="text-muted">Only companies without a user are listed (one user per company). The company creates additional team members from its own login.</small>
            @endif
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Password @unless($user->exists)<span class="text-danger">*</span>@endunless</label>
              <input type="text" name="password" id="pwdField" class="form-control @error('password') is-invalid @enderror"
                     value="{{ old('password') }}"
                     placeholder="{{ $user->exists ? 'Leave blank to keep current' : 'Minimum 8 characters' }}" @required(! $user->exists)>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          @unless ($user->exists)
            <div class="form-check form-switch mb-3">
              <input type="hidden" name="auto_password" value="0">
              <input class="form-check-input" type="checkbox" role="switch" id="auto_password" name="auto_password" value="1"
                     @checked(old('auto_password'))
                     onchange="const p=document.getElementById('pwdField'); p.disabled=this.checked; p.required=!this.checked; p.placeholder=this.checked?'Will be generated automatically':'Minimum 8 characters';">
              <label class="form-check-label" for="auto_password">Auto-generate a secure password</label>
            </div>
            <p class="text-muted" style="font-size:13px;">The login URL, email and password are emailed to the user automatically.</p>
          @endunless

          @if ($user->exists)
            <div class="mb-3" style="max-width:240px;">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                  <option value="{{ $status }}" @selected(old('status', $user->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Save User' : 'Create User' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
