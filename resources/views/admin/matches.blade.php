<h2>Matches Pending Verification</h2>

@foreach($matches as $match)
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <h3>Match #{{ $match->id }}</h3>
        <p><strong>Lost Item:</strong> {{ $match->lost->item_name }} ({{ $match->lost->location ?? 'No location' }})</p>
        <p><strong>Found Item:</strong> {{ $match->found->item_name }} ({{ $match->found->location ?? 'No location' }})</p>
        
        <form action="{{ route('admin.matches.verify', $match->id) }}" method="POST">
            @csrf
            <button type="submit" name="action" value="approve" class="btn btn-success">Approve Match</button>
            <button type="submit" name="action" value="reject" class="btn btn-danger">Reject Match</button>
        </form>
    </div>
@endforeach

@if($matches->isEmpty())
    <p>No matches pending verification.</p>
@endif