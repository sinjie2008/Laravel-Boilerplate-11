<p>Hello {{ $invoice->user->name }},</p>
<p>Your refund for invoice #{{ $invoice->id }} has been processed.</p>
<p>Amount: ${{ number_format($invoice->amount, 2) }}</p>
