{{-- Flash a SweetAlert2 toast from session('sweetalert') => ['icon','title','text'] --}}
@if (session('sweetalert') || $errors->any())
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof Swal === 'undefined') return;
      @if (session('sweetalert'))
        @php($sa = session('sweetalert'))
        Swal.fire({
          icon: @json($sa['icon'] ?? 'success'),
          title: @json($sa['title'] ?? 'Done'),
          text: @json($sa['text'] ?? ''),
          timer: 2600,
          showConfirmButton: false,
          toast: true,
          position: 'top-end',
        });
      @endif
      @if ($errors->any() && ! $errors->has('email'))
        Swal.fire({ icon: 'error', title: 'Please check the form', text: @json($errors->first()) });
      @endif
    });
  </script>
@endif
