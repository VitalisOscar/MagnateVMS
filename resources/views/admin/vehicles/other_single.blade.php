@extends('admin.root')

@section('title', 'Vehicle Activity - '.$vehicle->registration_no.' ('.$vehicle->description.')')

@section('page_heading', 'Vehicle Activity')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.vehicles.other')}}" class="breadcrumb-item active">Other Vehicles</a>
    <a class="breadcrumb-item active">{{ $vehicle->registration_no }}</a>
</div>

<div class="bg-white border rounded mb-4">
    <div class="">

        <div class="px-4 py-4 border-bottom">
            @php
                if($vehicle->vehicleable_type == "staff"){
                    $link = route('admin.staff.checkins', ['keyword' => $vehicle->vehicleable->name]);
                }else{
                    $link = route('admin.visitors.single', $vehicle->vehicleable->id);
                }
            @endphp
            <h5 class="mb-0">{{ $vehicle->description.' - ' }} <a href="{{ $link }}">{{ $vehicle->vehicleable->name }}</a> </h5>
        </div>

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Vehicle Usage Records ({{ $result->total.' Total' }})</h4>

            <?php $r = request(); ?>

            <div class="dropdown ml-auto">
                <button class="ml-auto mr-0 btn btn-primary btn-sm shadow-none dropdown-toggle" data-toggle="dropdown">Export to Excel</button>
                <ul class="dropdown-menu" id="export">
                    <li class="dropdown-item" title="Export all records">
                        <a class="dropdown-link" href="{{ route('admin.exports.other_vehicles_activity', ['vehicle_id' => $vehicle->id, 'filters' => 0]) }}">Export All</a>
                    </li>

                    <li class="dropdown-item" title="Add selected filters e.g date and sorting before exporting">
                        <a class="dropdown-link" href="{{ route('admin.exports.other_vehicles_activity', array_merge($r->all(), ['vehicle_id' => $vehicle->id, 'filters' => 1])) }}">Add Selected Options</a>
                    </li>
                </ul>
            </div>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <input type="readonly" placeholder="Any Date" id="flatpickr" class="form-control flatpickr mr-3" name="date" value="{{ $dates }}">

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
        </form>

        <table class="table">
            <tr class="card-header">
                <th>Site</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Checked In By</th>
                <th>Time Out</th>
                <th>Checked Out By</th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="6">
                    <p class="my-0">
                        No activity has been captured that matches the selected options
                    </p>
                </td>
            </tr>

            @else

            @foreach ($result->items as $activity)
            <tr>
                <td>{{ $activity->site->name }}</td>
                <td>{{ $activity->date }}</td>
                <td>{{ $activity->time_in ? \Carbon\Carbon::createFromTimeString($activity->time_in)->format('H:i'):'-' }}</td>
                <td>{{ $activity->check_in_user ? $activity->check_in_user->name : '-' }}</td>
                <td>{{ $activity->time_out ? \Carbon\Carbon::createFromTimeString($activity->time_out)->format('H:i'):'-' }}</td>
                <td>{{ $activity->check_out_user ? $activity->check_out_user->name : '-' }}</td>
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
