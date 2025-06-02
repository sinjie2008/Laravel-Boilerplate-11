@extends('adminlte::page')

@section('title', 'View Subscription')

@section('content_header')
    <h1>View Subscription for: {{ $subscription->user->name }}</h1>
@stop

@section('js')
    <script>
        function copyApiToken() {
            const tokenElement = document.getElementById('apiTokenDisplay');
            if (tokenElement) {
                const token = tokenElement.innerText;
                navigator.clipboard.writeText(token).then(() => {
                    alert('API Token copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy token: ', err);
                    alert('Failed to copy token. Please copy manually.');
                });
            }
        }
    </script>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Subscription Details</h3>
            <div class="card-tools">
                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr><th style="width: 200px;">ID</th><td>{{ $subscription->id }}</td></tr>
                    <tr><th>User</th><td>{{ $subscription->user->name }} ({{ $subscription->user->email }})</td></tr>
                    <tr><th>Plan</th><td>{{ $subscription->plan->name }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'pending' ? 'warning' : ($subscription->status === 'expired' ? 'secondary' : 'danger')) }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr><th>Starts At</th><td>{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d H:i:s') : 'N/A' }}</td></tr>
                    <tr><th>Ends At</th><td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d H:i:s') : 'N/A' }}</td></tr>
                    <tr><th>Plan Permissions</th>
                        <td>
                            @forelse ($subscription->plan->permissions as $permission)
                                <span class="badge badge-info">{{ $permission->name }}</span>
                            @empty
                                <span class="badge badge-secondary">No permissions for this plan</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th>API Token Status</th>
                        <td>
                            @if ($subscription->status === 'active' || $isSuperAdmin)
                                @if ($apiTokenExists)
                                    <span class="badge badge-success">Token Active (Name: {{ $tokenName }})</span>
                                    <form action="{{ route('admin.subscriptions.regenerateToken', $subscription) }}" method="POST" style="display: inline-block; margin-left: 10px;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-warning" onclick="return confirm('Are you sure you want to regenerate the API token? The current token will be invalidated.')">Regenerate Token</button>
                                    </form>
                                @else
                                    <span class="badge badge-warning">Token Not Found</span>
                                     <form action="{{ route('admin.subscriptions.regenerateToken', $subscription) }}" method="POST" style="display: inline-block; margin-left: 10px;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-success">Generate Token</button>
                                    </form>
                                @endif
                            @else
                                <span class="badge badge-secondary">Subscription not active, token not applicable/revoked.</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Created At</th><td>{{ $subscription->created_at->format('Y-m-d H:i:s') }}</td></tr>
                    <tr><th>Updated At</th><td>{{ $subscription->updated_at->format('Y-m-d H:i:s') }}</td></tr>
                    @if($token)
                        <tr>
                            <th>Generated API Token</th>
                                <td>
                                    <code id="apiTokenDisplay" style="word-break: break-all;">{{ $token }}</code>
                                    <button class="btn btn-xs btn-outline-primary ml-2" onclick="copyApiToken()">Copy Token</button>
                                    <p class="text-muted mt-2">
                                        <strong>Important:</strong> This token is displayed only once for security reasons. Please copy it now. It is not stored in plain text.
                                    </p>
                                </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">How to Use the API Token</h3>
        </div>
        <div class="card-body">
            <p>The API token generated for a user's active subscription can be used to authenticate API requests. This token grants access to resources based on the permissions associated with the user's active plan.</p>
            <p>To use the token, include it in the <code>Authorization</code> header of your HTTP requests as a Bearer token:</p>
            <pre><code class="language-http">Authorization: Bearer YOUR_API_TOKEN_HERE</code></pre>
            <p>Example using JavaScript (Fetch API):</p>
            <pre><code class="language-javascript">
const apiToken = '{{ $token ?? 'YOUR_GENERATED_TOKEN' }}'; // Use the PHP $token or a placeholder
const apiUrl = '{{ url('/api/v1/services') }}'; // Example API endpoint

// This script is for demonstration purposes. In a real application,
// you would typically place this logic in a separate JavaScript file
// and pass the token securely.

fetch(apiUrl, {
  method: 'GET',
  headers: {
    'Accept': 'application/json',
    'Authorization': `Bearer ${apiToken}`
  }
})
.then(response => {
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.json();
})
.then(data => {
  console.log('API Response for /api/v1/services:', data);
  // You can display the response data on the page here if needed
})
.catch(error => {
  console.error('Error fetching data from /api/v1/services:', error);
  // Display error message on the page
});
</code></pre>
            <p>The <code>apiToken</code> variable above will automatically contain the generated token if you were redirected to this page immediately after creating a subscription. Otherwise, you will need to manually replace <code>YOUR_GENERATED_TOKEN</code> with a valid token.</p>
            <p>The <code>apiUrl</code> is set to an example endpoint <code>{{ url('/api/v1/services') }}</code>. You can replace this with any other API endpoint relevant to your application.</p>
            <p>The token's validity and the resources it can access are tied to the subscription's status and the plan's permissions. If the subscription becomes inactive or the plan changes, the token's abilities may be revoked or the token itself may be invalidated.</p>
        </div>
    </div>
@stop
