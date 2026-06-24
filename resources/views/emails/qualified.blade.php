<x-mail::message>
# Congratulations!

Your application to **{{ config('school.name') }}** has been approved. You are now a bona fide student.

Use the credentials below to log in:

<table width="100%" cellpadding="0" cellspacing="0" style="margin:4px 0 6px;"><tr>
<td style="background-color:#f0faf8;border:1px solid #c3e8e2;border-left:4px solid #0d6e5f;border-radius:10px;padding:18px 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<div style="font-size:11px;color:#5a7a75;text-transform:uppercase;letter-spacing:.6px;">School ID</div>
<div style="font-size:20px;font-weight:800;color:#0f172a;font-family:'Courier New',monospace;padding-bottom:12px;">{{ $schoolId }}</div>
<div style="font-size:11px;color:#5a7a75;text-transform:uppercase;letter-spacing:.6px;">Default Password</div>
<div style="font-size:20px;font-weight:800;color:#0f172a;font-family:'Courier New',monospace;">{{ $password }}</div>
</td>
</tr></table>

<x-mail::button :url="$loginUrl">Log In to Your Account</x-mail::button>

<p style="font-size:13px;color:#6b7280;line-height:1.6;">For your security, you'll be asked to change your password on first login. You can then proceed to enroll for the semester.</p>
</x-mail::message>
