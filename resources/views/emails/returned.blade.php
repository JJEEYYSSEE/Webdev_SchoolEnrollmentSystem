<x-mail::message>
# Action Needed on Your Application

Your application to **{{ config('school.name') }}** needs a correction before it can be approved:

<table width="100%" cellpadding="0" cellspacing="0" style="margin:4px 0 6px;"><tr>
<td style="background-color:#fff8ec;border:1px solid #fde2b3;border-left:4px solid #f59e0b;border-radius:10px;padding:16px 18px;font-size:15px;line-height:1.6;color:#92400e;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<strong style="color:#b45309;">Registrar's note:</strong> {{ $remarks }}
</td>
</tr></table>

<x-mail::button :url="$fixUrl">Fix My Application</x-mail::button>

<p style="font-size:13px;color:#6b7280;line-height:1.6;">Log in, re-upload the correct document(s), and resubmit. Your password is unchanged.</p>
</x-mail::message>
