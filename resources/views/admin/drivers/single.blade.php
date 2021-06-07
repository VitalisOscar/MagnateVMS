@extends('admin.root')

@section('title', 'Vehicle Detail - '.$vehicle->registration_no.' ('.$vehicle->description.')')

@section('page_heading', 'Vehicle Overview')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.vehicles')}}" class="breadcrumb-item active">Vehicles</a>
    <a class="breadcrumb-item active">{{ $vehicle->registration_no }}</a>
</div>

<div class="bg-white border rounded mb-4">
    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Most Recent Logins</h4>
            <a href="" class="ml-auto btn btn-primary btn-sm shadow-none">View All</a>
        </div>

        <table class="table">
            <tr class="card-header">
                <th>#</th>
                <th>Site</th>
                <th>Time</th>
                <th>Outcome</th>
            </tr>

            <tr>
                <td colspan="4">
                    <p class="my-0">
                 has not logged in recently at any site
                    </p>
                </td>
            </tr>
        </table>

    </div>
</div>

@endsection
