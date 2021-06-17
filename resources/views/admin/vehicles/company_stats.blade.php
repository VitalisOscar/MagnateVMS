@extends('admin.root')

@section('title', 'Company Vehicles Activity')

@section('page_heading', 'Company Vehicles Activity')

@section('links')
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.vehicles') }}"class="breadcrumb-item">Company Vehicles</a>
    <a class="breadcrumb-item active">Activity</a>
</div>

<div class="bg-white border rounded mb-4">
    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">All Records ({{ $result->total.' Total' }})</h4>

            <?php $r = request(); ?>

            <div class="dropdown ml-auto">
                <button class="ml-auto mr-0 btn btn-primary btn-sm shadow-none dropdown-toggle" data-toggle="dropdown">Export to Excel</button>
                <ul class="dropdown-menu" id="export">
                    <li class="dropdown-item" title="Export all records">
                        <a class="dropdown-link" href="{{ route('admin.exports.company_vehicles_activity', ['filters' => 0]) }}">Export All</a>
                    </li>

                    <li class="dropdown-item" title="Add selected filters e.g date and sorting before exporting">
                        <a class="dropdown-link" href="{{ route('admin.exports.company_vehicles_activity', array_merge($r->all(), ['filters' => 1])) }}">Add Selected Options</a>
                    </li>
                </ul>
            </div>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <input type="search" name="keyword" class="form-control bg-white mr-3" placeholder="Seach here..." value="{{ $r->filled('keyword') ? $r->get('keyword'):'' }}">

            <input type="readonly" placeholder="Any Date" id="flatpickr" class="form-control flatpickr mr-3" name="date" value="{{ $dates }}">

            <div class="d-flex align-items-center ml-auto">
                <select name="limit" class="custom-select mr-3" style="width: auto !important">
                    <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                    <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                    <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                    <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
                </select>

                <select name="order" class="custom-select mr-3" style="width: auto !important">
                    <option value="">Latest Records First</option>
                    <option value="past" @if($r->get('order') == 'past'){{ __('selected') }}@endif>Past Records First</option>
                </select>

                <button class="btn btn-default shadow-none">Go</button>
            </div>
        </form>

        <table class="table">
            <tr class="card-header">
                <th>Vehicle</th>
                <th>Checked Out</th>
                <th>Driver Out</th>
                <th>Fuel Out</th>
                <th>Fuel In</th>
                <th>Mileage Out</th>
                <th>Mileage In</th>
                <th>Checked In</th>
                <th>Driver In</th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="9">
                    <p class="my-0">
                        No visits have been captured that match the selected options
                    </p>
                </td>
            </tr>

            @else

            @foreach ($result->items as $drive)
            <tr>
                <td>{{ $drive->vehicle->registration_no.' ('.$drive->vehicle->description.')' }}</td>
                <td>{{ $drive->check_out }}</td>

                @php
                    $out = 'Not Captured';
                    if($drive->driveable_out_type == 'driver'){
                        $out = $drive->driveable_out->name.' - '.$drive->driveable_out->department;
                        // $out_link = route('admin.vehicles.drivers.single', $drive->driveable_out->id);
                    }else if($drive->driveable_out_type == 'staff'){
                        $out = $drive->driveable_out->name.' (Staff)';
                        // $out_link = route('admin.staff', $drive->driveable_out->id);
                    }
                @endphp

                <td>{{ $out }}</td>
                <td>{{ number_format($drive->fuel_out) }}</td>
                <td>{{ number_format($drive->fuel_in) }}</td>

                <td>{{ number_format($drive->mileage_out) }}</td>
                <td>{{ number_format($drive->mileage_in) }}</td>

                <td>{{ $drive->check_in }}</td>

                @php
                    $in = 'Not Captured';
                    if($drive->driveable_in_type == 'driver'){
                        $in = $drive->driveable_in->name.' - '.$drive->driveable_in->department;
                    }else if($drive->driveable_in_type == 'staff'){
                        $in = $drive->driveable_in->name.' (Staff)';
                    }
                @endphp

                <td>{{ $in }}</td>
            </tr>
            @endforeach

            @php
                $route = \Illuminate\Support\Facades\Route::current();
                $prev = array_merge($route->parameters, $r->except('page'), ['page' => $result->prev_page]);
                $next = array_merge($route->parameters, $r->except('page'), ['page' => $result->next_page]);
            @endphp

            <tr>
                <td colspan="8">
                    <div class="d-flex align-items-center">
                        <a href="{{ route($route->getName(), $prev) }}" class="@if(!$result->hasPreviousPage()){{ __('disabled') }}@endif mr-auto btn btn-link p-0"><i class="fa fa-angle-double-left"></i>&nbsp;Prev</a>
                        <span>{{ 'Page '.$result->page.' of '.$result->max_pages }}</span>
                        <a href="{{ route($route->getName(), $next) }}" class="@if(!$result->hasNextPage()){{ __('disabled') }}@endif ml-auto btn btn-link p-0">Next&nbsp;<i class="fa fa-angle-double-right"></i></a>
                    </div>
                </td>
            </tr>

            @endif
        </table>

    </div>
</div>


@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#flatpickr", {
            mode: 'range',
            maxDate: 'today'
        });
    </script>
@endsection
