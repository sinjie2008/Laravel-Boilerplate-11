<h1>Invoice {{ $invoice->id }}</h1>
<p>Total: {{ $invoice->total() }} {{ strtoupper($invoice->currency) }}</p>
