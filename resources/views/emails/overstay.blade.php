<p>
    Hi {{ $name }},
</p>
<p>
    Number of Overstay Records: <b>
        @if ($list != null)
            {{ $list->count() }}
        @endif
    </b>
</p>

<p>
<table border="1">
    <tr>
        <td>No.</td>
        <td>Name</td>
        <td>Entry Time</td>
        <td>Exit Time</td>
        <td>Overstay</td>
    </tr>
    @if ($list != null)
        @foreach ($list as $index => $log)
            <td>{{ $index + 1 }}</td>
            <td>{{ $log->employee->name ?? '' }}</td>
            <td>{{ $log->enter_time }}</td>
            <td>{{ $log->exit_time }}</td>
            <td>{{ getHoursMinutes($log->actual_stay_duration_seconds, false) }}</td>
        @endforeach
    @endif
</table>
</p>
