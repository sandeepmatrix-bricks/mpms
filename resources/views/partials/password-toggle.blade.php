{{-- Adds a show/hide eye toggle to every real password field on the page. --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="password"]').forEach(function (input) {
      if (input.dataset.pwToggle) return;
      input.dataset.pwToggle = '1';

      var wrap = document.createElement('span');
      wrap.style.cssText = 'position:relative; display:block;';
      input.parentNode.insertBefore(wrap, input);
      wrap.appendChild(input);
      input.style.paddingRight = '40px';

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.setAttribute('tabindex', '-1');
      btn.setAttribute('aria-label', 'Show password');
      btn.innerHTML = '<i class="fa fa-eye"></i>';
      btn.style.cssText = 'position:absolute; top:50%; right:12px; transform:translateY(-50%); border:0; background:transparent; color:#6c757d; cursor:pointer; padding:0; z-index:5; line-height:1;';
      wrap.appendChild(btn);

      btn.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      });
    });
  });
</script>
