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
            <h4 class="font-weight-600 mb-0">In/Out Records ({{ $result->total.' Total' }})</h4>

            <?php $r = request(); ?>

            <div class="dropdown ml-auto d-none">
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
            <input type="search" name="keyword" class="form-control bg-white mr-3" placeholder="Driver..." value="{{ $r->filled('keyword') ? $r->get('keyword'):'' }}">

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
                <th>Date</th>
                <th>Time</th>
                <th>Site</th>
                <th>Driver</th>
                <th>Task</th>
                <th>Type</th>
                <th>Mileage</th>
                <th>Guard</th>
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
                <td>{{ $activity->fmt_date }}</td>
                <td>{{ $activity->fmt_time }}</td>
                <td>{{ $activity->site->name }}</td>
                <td>{{ $activity->driver_task->driver->name }}</td>
                <td>{{ $activity->driver_task->task }}</td>
                <td>{{ $activity->type }}</td>
                <td>{{ $activity->driver_task->fmt_mileage }}</td>
                <td>{{ $activity->user->name }}</td>
            </tr>
            @endforeach

            <tr>
                <td colspan="6">
                    <div class="d-flex align-items-center">
                        <a href="{{ $result->prevPageUrl() }}" class="@if(!$result->hasPreviousPage()){{ __('disabled') }}@endif mr-auto btn btn-link p-0"><i class="fa fa-angle-double-left"></i>&nbsp;Prev</a>
                        <span>{{ 'Page '.$result->page.' of '.$result->max_pages }}</span>
                        <a href="{{ $result->nextPageUrl() }}" class="@if(!$result->hasNextPage()){{ __('disabled') }}@endif ml-auto btn btn-link p-0">Next&nbsp;<i class="fa fa-angle-double-right"></i></a>
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
