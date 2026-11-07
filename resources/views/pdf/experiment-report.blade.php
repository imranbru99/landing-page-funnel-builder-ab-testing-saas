<html><body style="font-family: DejaVu Sans, sans-serif;">
<h1>Experiment Report</h1>
<p>{{ $organization->name }} — {{ $experiment->name }}</p>
<table width="100%" border="1" cellspacing="0" cellpadding="6">
<tr><th>Variant</th><th>Visitors</th><th>Conversions</th><th>Rate</th><th>Lift</th></tr>
@foreach($summary['variants'] as $row)
<tr>
<td>{{ $row['name'] }}</td>
<td>{{ $row['visitors'] }}</td>
<td>{{ $row['conversions'] }}</td>
<td>{{ $row['conversion_rate_pct'] }}%</td>
<td>{{ $row['lift_pct'] ?? '—' }}</td>
</tr>
@endforeach
</table>
</body></html>