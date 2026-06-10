<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color: #2b2b2b;">
  <p>Hi {{ $recipientName }},</p>

  <p><strong>{{ $authorName }}</strong> mentioned you in the Activity Chat for applicant
     <strong>{{ $applicantName }}</strong>:</p>

  <blockquote style="border-left: 4px solid #0d6e6e; margin: 16px 0; padding: 8px 16px; background: #f4f8f8;">
    {{ $body }}
  </blockquote>

  <p>
    <a href="{{ $url }}" style="background:#0d6e6e; color:#fff; padding:10px 18px; text-decoration:none; border-radius:4px; display:inline-block;">
      Open applicant
    </a>
  </p>

  <p style="color:#888; font-size:12px;">You received this because you were mentioned with @.</p>
</body>
</html>
