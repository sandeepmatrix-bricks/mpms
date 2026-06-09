@props(['action', 'label' => 'Delete'])

<form method="POST" action="{{ $action }}" class="d-inline"
      onsubmit="return confirm('Are you sure? This action cannot be undone.');">
  @csrf
  @method('DELETE')
  <button type="submit" class="btn btn-sm btn-danger" title="{{ $label }}">
    Delete
  </button>
</form>
