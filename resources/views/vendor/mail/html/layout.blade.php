<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('school.name', 'CISHS') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body, #body-table {
            background-color: #071f18 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: #1a5c50;
            margin-bottom: 16px;
            display: block;
        }
        p {
            font-size: 15px;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #1a5c50 !important;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 8px;
        }
        .subcopy {
            border-top: 1px solid #e5e7eb;
            margin-top: 28px;
            padding-top: 16px;
            font-size: 13px;
            color: #6b7280;
        }
        .subcopy a { color: #1a5c50; }
    </style>
</head>
<body bgcolor="#071f18" style="background-color:#071f18; margin:0; padding:0;">

<!-- Outer table -->
<table id="body-table" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#071f18"
       style="background-color:#071f18; min-height:100vh;">
    <tr>
        <td align="center" valign="top" style="padding: 32px 16px 48px;">

            <!-- Inner container -->
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="max-width:600px; width:100%;">

                <!-- Header -->
                <tr>
                    <td bgcolor="#1a5c50" style="background-color:#1a5c50; border-radius:12px 12px 0 0; padding:20px 32px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="color:#ffffff; font-size:18px; font-weight:700; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; letter-spacing:0.5px;">
                                    CISHS
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td bgcolor="#ffffff" style="background-color:#ffffff; border-radius:0 0 12px 12px; padding:36px 40px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
                        {{ Illuminate\Mail\Markdown::parse($slot) }}

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td align="center" style="padding-top:24px; font-size:12px; color:#a7f3d0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
                        powered by E-Tala Enrollment System &nbsp;·&nbsp; {{ config('school.name', 'Cabrivex International Senior High School') }}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>