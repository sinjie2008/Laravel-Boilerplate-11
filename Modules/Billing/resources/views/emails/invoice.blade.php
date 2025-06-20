<p>Hello {{ $invoice->user->name }},</p>
<p>We created invoice #{{ $invoice->id }} for ${{ number_format($invoice->amount, 2) }}.</p>
