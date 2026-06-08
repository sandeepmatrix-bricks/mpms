@props(['action', 'label' => 'Delete'])

<form method="POST" action="{{ $action }}" class="d-inline"
      onsubmit="return confirm('Are you sure? This action cannot be undone.');">
  @csrf
  @method('DELETE')
  <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ $label }}">
    <i data-feather="trash-2" style="width:15px;height:15px;"></i>
  </button>
</form>
