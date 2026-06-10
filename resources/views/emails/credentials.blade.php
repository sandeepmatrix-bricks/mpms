<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f5f7; padding:24px; color:#2b2f36;">
  <div style="max-width:520px; margin:0 auto; background:#fff; border-radius:10px; overflow:hidden; border:1px solid #e7e9ef;">
    <div style="background:#5c6bc0; color:#fff; padding:20px 28px;">
      <h2 style="margin:0; font-size:18px;">Welcome{{ $tenant ? ' to '.$tenant->name : '' }}</h2>
    </div>
    <div style="padding:24px 28px;">
      <p>Hi {{ $name }},</p>
      <p>An account has been created for you. Use the credentials below to sign in. For your security you'll be asked to set a new password the first time you log in.</p>

      <table style="width:100%; border-collapse:collapse; margin:18px 0;">
        <tr>
          <td style="padding:10px 12px; background:#f4f5f7; border-radius:6px 0 0 6px; width:120px; font-weight:bold;">Email</td>
          <td style="padding:10px 12px; background:#f4f5f7; border-radius:0 6px 6px 0;">{{ $email }}</td>
        </tr>
        <tr><td style="height:8px;"></td></tr>
        <tr>
          <td style="padding:10px 12px; background:#f4f5f7; border-radius:6px 0 0 6px; font-weight:bold;">Password</td>
          <td style="padding:10px 12px; background:#f4f5f7; border-radius:0 6px 6px 0; font-family:monospace; font-size:15px;">{{ $password }}</td>
        </tr>
      </table>

      <p style="text-align:center; margin:26px 0;">
        <a href="{{ $loginUrl }}" style="background:#5c6bc0; color:#fff; text-decoration:none; padding:12px 26px; border-radius:6px; display:inline-block; font-weight:bold;">Log in</a>
      </p>

      <p style="font-size:13px; color:#7a7f88;">If the button doesn't work, paste this URL into your browser:<br>{{ $loginUrl }}</p>
    </div>
  </div>
</body>
</html>
