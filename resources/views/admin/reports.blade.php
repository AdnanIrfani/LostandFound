@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>System Reports</h2>
            <p class="text-muted">Overview of lost & found activities</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Users</h6>
                    <h3>{{ \App\Models\User::count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Lost Items</h6>
                    <h3>{{ \App\Models\LostItem::count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Found Items</h6>
                    <h3>{{ \App\Models\FoundItem::count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Verified Matches</h6>
                    <h3>{{ \App\Models\ItemMatch::where('match_status', 1)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Detailed Statistics</strong>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    Pending Matches:
                    <strong>
                        {{ \App\Models\ItemMatch::where('match_status', 0)->count() }}
                    </strong>
                </li>
                <li class="list-group-item">
                    Recovered Items:
                    <strong>
                        {{ \App\Models\SuccessStory::count() }}
                    </strong>
                </li>
                <li class="list-group-item">
                    Active Categories:
                    <strong>
                        {{ \App\Models\Category::where('is_active', 1)->count() }}
                    </strong>
                </li>
            </ul>
        </div>
    </div>

</div>
@endsection
