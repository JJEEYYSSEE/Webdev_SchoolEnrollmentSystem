<x-mail::message>
# Verify Your Email Address

Thanks for registering with **{{ config('school.name') }}**! Please confirm your email address to continue your Grade 11 application.

<x-mail::button :url="$url">Verify Email Address</x-mail::button>

<p style="font-size:13px;color:#6b7280;line-height:1.6;">If you didn't create an account, no further action is required.</p>
</x-mail::message>
