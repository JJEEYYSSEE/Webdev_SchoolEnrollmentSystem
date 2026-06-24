<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate of Registration — {{ $student->fullName() }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            color: #1f2937;
            background: #f3f4f6;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        .sheet {
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 48px;
            box-shadow: 0 1px 6px rgba(0,0,0,.12);
        }
        .toolbar {
            max-width: 800px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn {
            font: inherit;
            border: 0;
            border-radius: 6px;
            padding: 9px 18px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print { background: #15803d; color: #fff; }
        .btn-back  { background: #e5e7eb; color: #374151; }

        .head { text-align: center; border-bottom: 2px solid #15803d; padding-bottom: 16px; }
        .head h1 { font-size: 18px; letter-spacing: .5px; text-transform: uppercase; color: #14532d; }
        .head .sub { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .head .doc-title { margin-top: 12px; font-size: 15px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .head .sy { font-size: 12px; color: #374151; }

        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 32px; margin: 20px 0; }
        .meta .row { display: flex; gap: 8px; }
        .meta .label { color: #6b7280; min-width: 110px; }
        .meta .val { font-weight: 600; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 7px 10px; text-align: left; }
        th { background: #f0fdf4; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: #166534; }
        td.center, th.center { text-align: center; }
        tfoot td { font-weight: 700; background: #f9fafb; }

        .section-title { margin-top: 24px; font-size: 13px; font-weight: 700; color: #14532d; text-transform: uppercase; letter-spacing: .5px; }

        .signoff { margin-top: 48px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .sign { text-align: center; }
        .sign .line { border-top: 1px solid #374151; margin-top: 36px; padding-top: 6px; font-weight: 600; }
        .sign .role { font-size: 11px; color: #6b7280; }

        .foot { margin-top: 36px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 12px; }

        @media print {
            body { background: #fff; padding: 0; font-size: 12px; }
            .sheet { box-shadow: none; max-width: none; padding: 24px; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>
    @php
        $subjects   = $enrollment->section->subjects;
        $totalUnits = $subjects->sum('units');
        $fmt = fn ($t) => $t ? \Carbon\Carbon::parse($t)->format('g:i A') : null;
    @endphp

    <div class="toolbar">
        <a href="{{ route('student.showEnrollStatus') }}" class="btn btn-back">&larr; Back</a>
        <button onclick="window.print()" class="btn btn-print">Print / Save as PDF</button>
    </div>

    <div class="sheet">
        <div class="head">
            <h1>{{ config('school.name') }}</h1>
            <div class="sub">Senior High School Department &middot; {{ config('school.short') }}</div>
            <div class="doc-title">Certificate of Registration</div>
            <div class="sy">
                S.Y. {{ $enrollment->section->schoolYear->year_label ?? '' }} &middot;
                {{ $enrollment->section->semester }} Semester
            </div>
        </div>

        <div class="meta">
            <div class="row"><span class="label">Student Name</span><span class="val">{{ $student->fullName() }}</span></div>
            <div class="row"><span class="label">School ID</span><span class="val">{{ $student->student_number ?? '—' }}</span></div>
            <div class="row"><span class="label">Grade &amp; Strand</span><span class="val">Grade {{ $student->grade_level }} &middot; {{ $enrollment->section->strand->strand_code ?? '' }}</span></div>
            <div class="row"><span class="label">Section</span><span class="val">{{ $enrollment->section->section_name }}</span></div>
            <div class="row"><span class="label">Schedule Block</span><span class="val">{{ $enrollment->section->time_period }}</span></div>
            <div class="row"><span class="label">Date Enrolled</span><span class="val">{{ optional($enrollment->reviewed_at ?? $enrollment->submitted_at)->format('F d, Y') }}</span></div>
        </div>

        <div class="section-title">Enrolled Subjects</div>
        <table>
            <thead>
                <tr>
                    <th style="width:90px;">Code</th>
                    <th>Subject</th>
                    <th class="center" style="width:60px;">Units</th>
                    <th style="width:200px;">Schedule</th>
                    <th class="center" style="width:70px;">Room</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subjects as $subject)
                    <tr>
                        <td>{{ $subject->subject_code }}</td>
                        <td>{{ $subject->subject_name }}</td>
                        <td class="center">{{ $subject->units }}</td>
                        <td>
                            @if ($subject->pivot->start_time)
                                {{ $subject->pivot->day_of_week }}
                                {{ $fmt($subject->pivot->start_time) }}–{{ $fmt($subject->pivot->end_time) }}
                            @else
                                <span style="color:#9ca3af;">TBA</span>
                            @endif
                        </td>
                        <td class="center">{{ $subject->pivot->room ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center" style="color:#9ca3af;">No subjects on record.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Units</td>
                    <td class="center">{{ $totalUnits }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <div class="signoff">
            <div class="sign">
                <div class="line">{{ $student->fullName() }}</div>
                <div class="role">Student Signature</div>
            </div>
            <div class="sign">
                <div class="line">{{ $enrollment->approver?->fullName() ?? 'Office of the Registrar' }}</div>
                <div class="role">Registrar</div>
            </div>
        </div>

        <div class="foot">
            This is a system-generated Certificate of Registration. Valid only with the registrar's confirmation.<br>
            Powered by {{ config('school.platform') }} &middot; Generated {{ now()->format('F d, Y g:i A') }}
        </div>
    </div>
</body>
</html>
