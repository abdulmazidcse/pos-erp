@component('mail::message')
# Hello {{ $user->name }},

Your One-Time Password (OTP) for registration is:
 
<p style="width:30%;font-size:16px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:14px 32px 14px 32px;background-color:#f2f2f2;border-left:1px solid #ccc;border-right:1px solid #ccc;border-top:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;border-radius:7px;display:block;border:1px solid #1877f2;background:#e7f3ff">
    {{ $otp }}
</p>

Please use this OTP to complete your registration.

If you did not request this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent