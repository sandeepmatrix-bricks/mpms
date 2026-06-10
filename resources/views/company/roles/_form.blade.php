@php($selected = collect(old('permissions', $selected ?? []))->map(fn ($v) => (string) $v))
<div class="row">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $action }}">
          @csrf
          @if ($method !== 'POST') @method($method) @endif

          <div class="mb-3">
            <label class="form-label">Role name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $role->name) }}" placeholder="e.g. Manager, User" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <label class="form-label mb-1">Permissions</label>
          <p class="text-muted mb-2" style="font-size:13px;">
            Tick what this role can do per module. <strong>Read</strong> = view, <strong>Write</strong> = create,
            <strong>Edit</strong> = update, <strong>Delete</strong> = remove. Unticked permissions hide the tab and block the action.
          </p>

          <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Module</th>
                  @foreach ($actions as $action)
                    <th class="text-center text-capitalize">{{ $action }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($modules as $modKey => $modLabel)
                  <tr>
                    <td class="f-w-600">{{ $modLabel }}</td>
                    @foreach ($actions as $action)
                      @php($pid = $permMap[$modKey.'.'.$action] ?? null)
                      <td class="text-center">
                        @if ($pid)
                          <input class="form-check-input" type="checkbox" name="permissions[]"
                                 value="{{ $pid }}" @checked($selected->contains((string) $pid))>
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Role</button>
            <a href="{{ route('company.roles.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
