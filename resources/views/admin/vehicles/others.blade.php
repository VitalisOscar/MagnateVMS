@extends('admin.root')

@section('title', 'View non-company vehicles')

@section('page_heading', 'Non-company Vehicles')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">Vehicles</a>
</div>

<div class="p-0 bg-white border rounded">

    @php
        $r = request();
    @endphp

    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">{{ ($r->get('show') == 'staff' ? 'Staff':($r->get('show') == 'visitors' ? 'Visitor':'Other')).' Vehicles' }}</h4>

            <div class="dropdown ml-auto">
                <button class="ml-auto mr-0 btn btn-primary btn-sm shadow-none dropdown-toggle" data-toggle="dropdown">Export to Excel</button>
                <ul class="dropdown-menu" id="export">
                    <li class="dropdown-item" title="Export all records">
                        <a class="dropdown-link" href="{{ route('admin.exports.other_vehicles', ['filters' => 0]) }}">Export All</a>
                    </li>

                    <li class="dropdown-item" title="Add selected filters e.g date and sorting before exporting">
                        <a class="dropdown-link" href="{{ route('admin.exports.other_vehicles', array_merge($r->all(), ['filters' => 1])) }}">Add Selected Options</a>
                    </li>
                </ul>
            </div>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <input type="search" name="keyword" class="form-control bg-white mr-3" placeholder="Seach by name, registration no..." value="{{ $r->filled('keyword') ? $r->get('keyword'):'' }}">

            <select name="limit" class="custom-select mr-3" style="width: auto !important">
                <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
            </select>

            <select name="show" class="custom-select mr-3" style="width: auto !important">
                <option value="">Show All</option>
                <option value="staff" @if($r->get('show') == 'staff'){{ __('selected') }}@endif>Staff Vehicles</option>
                <option value="visitors" @if($r->get('show') == 'visitors'){{ __('selected') }}@endif>Visitor Vehicles</option>
            </select>

            <button class="btn btn-default shadow-none">Go</button>
        </form>

        <table class="table mb-0">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Owner</th>
                <th>Owner Name</th>
                <th>Registration Number</th>
                <th></th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="5">
                    <p class="my-0">
                        No vehicles matching the options. Once staff and visitor vehicles are checked in, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 0;
            @endphp
            @foreach ($result->items as $vehicle)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>{{ $vehicle->owner_type }}</td>
                <td>
                    <a href="{{ route('admin.vehicles.single', $vehicle->id) }}">{{ $vehicle->owner_name }}</a>
                </td>
                <td>{{ $vehicle->registration_no }}</td>
                <td>
                    <a href="{{ route('admin.vehicles.single', $vehicle->id) }}">View Vehicle&nbsp;<i class="fa fa-share"></i></a>
                </td>
            </tr>

            @php
                $i++;
            @endphp
            @endforeach

            <tr>
                <td colspan="7">
                    <div class="d-flex align-items-center">
                        <a href="{{ $result->prevPageUrl() }}" class="@if(!$result->hasPreviousPage()){{ __('disabled') }}@endif mr-auto btn btn-link p-0"><i class="fa fa-angle-double-left"></i>&nbsp;Prev</a>
                        <span>{{ 'Page '.$result->page }}</span>
                        <a href="{{ $result->nextPageUrl() }}" class="@if(!$result->hasNextPage()){{ __('disabled') }}@endif ml-auto btn btn-link p-0">Next&nbsp;<i class="fa fa-angle-double-right"></i></a>
                    </div>
                </td>
            </tr>

            @endif
        </table>

    </div>

</div>

@endsection
