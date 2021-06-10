@extends('admin.root')

@section('title', 'Visit History')

@section('page_heading', 'Visit History')

@section('links')
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.visitors') }}"class="breadcrumb-item">Visitors</a>
    <a class="breadcrumb-item active">Visit History</a>
</div>

<div class="bg-white border rounded mb-4">
    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Visit History ({{ $result->total.' Visit'.($result->total != 1 ? 's':'') }})</h4>

            <?php $r = request(); ?>

            <div class="dropdown ml-auto">
                <button class="ml-auto mr-0 btn btn-primary btn-sm shadow-none dropdown-toggle" data-toggle="dropdown">Export to Excel</button>
                <ul class="dropdown-menu" id="export">
                    <li class="dropdown-item" title="Export all records">
                        <a class="dropdown-link" href="{{ route('admin.exports.visits', ['filters' => 0]) }}">Export All</a>
                    </li>

                    <li class="dropdown-item" title="Add selected filters e.g date and sorting before exporting">
                        <a class="dropdown-link" href="{{ route('admin.exports.visits', array_merge($r->all(), ['filters' => 1])) }}">Add Selected Options</a>
                    </li>
                </ul>
            </div>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <select name="site" class="custom-select mr-3" style="width: auto !important">
                <option value="">All Sites</option>
                @foreach(\App\Models\Site::all() as $site)
                <option value="{{ $site->id }}" @if($r->get('site') == $site->id){{ __('selected') }}@endif>{{ $site->name }}</option>
                @endforeach
            </select>

            <input type="readonly" placeholder="Any Date" id="flatpickr" class="form-control flatpickr mr-3" name="date" value="{{ $dates }}">

            <div class="d-flex align-items-center ml-auto">
                <select name="limit" class="custom-select mr-3" style="width: auto !important">
                    <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                    <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                    <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                    <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
                </select>

                <select name="order" class="custom-select mr-3" style="width: auto !important">
                    <option value="">Latest Visits First</option>
                    <option value="past" @if($r->get('order') == 'past'){{ __('selected') }}@endif>Past Visits First</option>
                </select>

                <button class="btn btn-default shadow-none">Go</button>
            </div>
        </form>

        <table class="table">
            <tr class="card-header">
                <th>Site</th>
                <th>Visitor</th>
                <th>Reason</th>
                <th>Host</th>
                <th>Check In</th>
                <th>Items In</th>
                <th>Check Out</th>
                <th>Items Out</th>
                <th>Car</th>
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

            @foreach ($result->items as $visit)
            <tr>
                <td>{{ $visit->site->name }}</td>
                <td><a href="{{ route('admin.visitors.single', $visit->visitor->id) }}">{{ $visit->visitor->name }}</a></td>
                <td>{{ $visit->reason }}</td>
                <td>{{ $visit->host }}</td>
                <td>{{ $visit->check_in }}</td>
                <td>{{ $visit->items_in ? $visit->items_in:'None' }}</td>
                <td>{{ $visit->check_out }}</td>
                <td>{{ $visit->items_out ? $visit->items_out:'None' }}</td>
                <td>{{ $visit->car_registration ? $visit->car_registration:'None' }}</td>
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
