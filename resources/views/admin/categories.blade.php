@extends('layouts.admin') {{-- change if your layout name is different --}}

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Categories Report</h3>

    @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Total Items</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->items_count }}</td>
                            <td>{{ $category->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning">
            No categories found.
        </div>
    @endif
</div>
@endsection
