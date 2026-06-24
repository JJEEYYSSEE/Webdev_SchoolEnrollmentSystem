<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light only">
    <title>{{ config('school.name', 'CISHS') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body, #body-table {
            background-color: #07201a !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            -webkit-font-smoothing: antialiased;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        h1, .greeting {
            font-size: 22px;
            font-weight: 800;
            color: #0d6e5f;
            margin: 0 0 18px;
            line-height: 1.3;
        }
        h2 { font-size: 18px; font-weight: 700; color: #0d6e5f; margin: 0 0 12px; }
        p {
            font-size: 15px;
            line-height: 1.75;
            color: #374151;
            margin: 0 0 16px;
        }
        strong { color: #0f172a; font-weight: 700; }
        a { color: #0d6e5f; }
        .btn-wrap { text-align: center; margin: 30px 0 6px; }
        .btn {
            display: inline-block;
            background-color: #0d6e5f;
            background-image: linear-gradient(135deg, #0d6e5f 0%, #1aa086 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            padding: 14px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 18px rgba(13, 110, 95, 0.35);
        }
        .subcopy {
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
            padding-top: 16px;
            font-size: 13px;
            line-height: 1.6;
            color: #6b7280;
        }
        .subcopy a { color: #0d6e5f; word-break: break-all; }
    </style>
</head>
<body bgcolor="#07201a" style="background-color:#07201a; margin:0; padding:0;">

<table id="body-table" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#07201a"
       style="background-color:#07201a; background-image:linear-gradient(160deg, #0c3a2e 0%, #07201a 55%);">
    <tr>
        <td align="center" valign="top" style="padding: 44px 16px 52px;">

            <!-- Card -->
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="max-width:600px; width:100%;">

                <!-- Header (gradient + centered logo) -->
                <tr>
                    <td align="center" bgcolor="#0d6e5f"
                        style="background-color:#0d6e5f; background-image:linear-gradient(135deg, #0d6e5f 0%, #1aa086 100%); border-radius:18px 18px 0 0; padding:38px 32px 30px;">

                        <!-- Logo badge (CID-embedded PNG) -->
                        <img src="cid:logo" alt="{{ config('school.short', 'CISHS') }}" width="76" height="76"
                             style="display:block; width:76px; height:76px; border:0; outline:none; margin:0 auto;">

                        <div style="color:#ffffff; font-size:21px; font-weight:800; letter-spacing:.3px; margin-top:18px;">
                            {{ config('school.name', 'Cabrivex International Senior High School') }}
                        </div>
                        <div style="color:#d1fae5; font-size:13px; margin-top:6px;">
                            SHS Online Enrollment Portal
                        </div>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td bgcolor="#ffffff" style="background-color:#ffffff; border-radius:0 0 18px 18px; padding:40px 44px;">
                        {{ Illuminate\Mail\Markdown::parse($slot) }}
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding:24px 16px 0; font-size:12px; line-height:1.7; color:#6ee7b7;">
                        powered by <strong style="color:#a7f3d0;">E-Tala Enrollment System</strong><br>
                        <span style="color:#8aa39c;">&copy; {{ date('Y') }} {{ config('school.name', 'Cabrivex International Senior High School') }}</span>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
