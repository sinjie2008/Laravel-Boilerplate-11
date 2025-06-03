<a href="{{ route('serviceapi.services.show', $service->id) }}" class="btn btn-info btn-sm">View</a>
<a href="{{ route('serviceapi.services.edit', $service->id) }}" class="btn btn-primary btn-sm">Edit</a>
<form action="{{ route('serviceapi.services.destroy', $service->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
</form> 