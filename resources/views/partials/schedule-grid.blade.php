@php
    $days = ['Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday'];
    $scheduled = $subjects->filter(fn ($s) => $s->pivot->start_time);
    $slots = $scheduled
        ->map(fn ($s) => $s->pivot->start_time.'|'.$s->pivot->end_time)
        ->unique()->sort()->values();
@endphp

@if ($scheduled->isEmpty())
    <div class="text-muted small py-3 text-center">
        <i class="bi bi-calendar-x me-1"></i> No schedule generated yet.
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:110px;">Time</th>
                    @foreach ($days as $label) <th>{{ $label }}</th> @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($slots as $slot)
                    @php [$start, $end] = explode('|', $slot); @endphp
                    <tr>
                        <td class="small fw-semibold text-muted">
                            {{ \Carbon\Carbon::parse($start)->format('g:i A') }}<br>
                            <span class="text-secondary">{{ \Carbon\Carbon::parse($end)->format('g:i A') }}</span>
                        </td>
                        @foreach ($days as $code => $label)
                            @php $cell = $scheduled->first(fn ($s) => $s->pivot->day_of_week === $code && $s->pivot->start_time === $start); @endphp
                            <td class="{{ $cell ? 'bg-primary bg-opacity-10' : '' }}">
                                @if ($cell)
                                    <div class="fw-semibold small">{{ $cell->subject_code }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">{{ $cell->pivot->room }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
