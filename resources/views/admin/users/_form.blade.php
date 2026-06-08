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
              <label class="form-label">Password @if (! $user->exists)<span class="text-danger">*</span>@endif</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                     placeholder="{{ $user->exists ? 'Leave blank to keep current' : 'Minimum 8 characters' }}"
                     @required(! $user->exists)>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['active', 'inactive'] as $status)
                  <option value="{{ $status }}" @selected(old('status', $user->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check form-switch">
              <input type="hidden" name="is_admin" value="0">
              <input class="form-check-input" type="checkbox" role="switch" id="is_admin" name="is_admin" value="1"
                     @checked(old('is_admin', $user->is_admin))>
              <label class="form-check-label" for="is_admin">Platform administrator (can sign into this console)</label>
            </div>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save User</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
