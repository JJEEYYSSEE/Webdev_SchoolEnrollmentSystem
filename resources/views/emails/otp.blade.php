<x-mail::message>
# Password Change Code

We received a request to change the password on your account. Enter this verification code to confirm:

<table width="100%" cellpadding="0" cellspacing="0" style="margin:8px 0 6px;"><tr><td align="center">
<table border="0" cellpadding="0" cellspacing="0" align="center"><tr>
@foreach (str_split($code) as $digit)
<td align="center" valign="middle" width="44" height="52" style="width:44px;height:52px;border:2px solid #0d6e5f;border-radius:8px;text-align:center;font-size:24px;font-weight:800;color:#0d6e5f;background-color:#f0faf8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">{{ $digit }}</td>
<td width="8" style="width:8px;line-height:8px;font-size:8px;">&nbsp;</td>
@endforeach
</tr></table>
</td></tr></table>

This code expires in **10 minutes**.

<p style="font-size:13px;color:#6b7280;line-height:1.6;">If you didn't request this, ignore this email — your password will stay the same.</p>
</x-mail::message>
