<x-mail::message>
# Informe d'errors

S'han importat les següents líneas:

@if(!empty($logContent))
<pre style="white-space: pre-wrap; font-family: monospace;">{{ $logContent }}</pre>
@else
Cap error registrat.
@endif

Gràcies,<br>
{{ config('app.name') }}
</x-mail::message>
