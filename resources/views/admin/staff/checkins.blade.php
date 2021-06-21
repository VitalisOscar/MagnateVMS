@extends('admin.root')

@section('title', 'Staff Check In History')

@section('page_heading', 'Staff Check In History')

@section('links')
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}"class="breadcrumb-item">Staff</a>
    <a class="breadcrumb-item active">Check In History</a>
</div>

<div class="bg-white border rounded mb-4">
    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Check In History ({{ $result->total.' Total' }})</h4>

            <?php
                $r = request();
                $companies = \App\Models\Company::with('site')->get();
            ?>

            <div class="dropdown ml-auto">
                <button class="ml-auto mr-0 btn btn-primary btn-sm shadow-none dropdown-toggle" data-toggle="dropdown">Export to Excel</button>
                <ul class="dropdown-menu" id="export">
                    <li class="dropdown-item" title="Export all records">
                        <a class="dropdown-link" href="{{ route('admin.exports.checkins', ['filters' => 0]) }}">Export All</a>
                    </li>

                    <li class="dropdown-item" title="Add selected filters e.g date and sorting before exporting">
                        <a class="dropdown-link" href="{{ route('admin.exports.checkins', array_merge($r->all(), ['filters' => 1])) }}">Add Selected Options</a>
                    </li>
                </ul>
            </div>
        </div>

        <form class="px-4 pb-3">
            <div class="d-flex align-items-center mb-3">
                <select name="site" class="custom-select mr-3" style="width: auto !important">
                    <option value="">At Any Site</option>
                    @foreach(\App\Models\Site::all() as $site)
                    <option value="{{ $site->id }}" @if($r->get('site') == $site->id){{ __('selected') }}@endif>{{ 'At '.$site->name }}</option>
                    @endforeach
                </select>

                <select name="company" class="custom-select mr-3" style="width: auto !important">
                    <option value="">Based at any company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" @if($r->get('company') == $company->id){{ __('selected') }}@endif>{{ 'Based at '.$company->name.' ('.$company->site->name.')' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex align-items-center">
                <input type="search" name="keyword" class="form-control bg-white mr-3" placeholder="Name, Phone Number..." value="{{ $r->filled('keyword') ? $r->get('keyword'):'' }}">

                <input type="readonly" placeholder="Any Date" id="flatpickr" class="form-control flatpickr mr-3 w-auto" name="date" value="{{ $dates }}">

                <div class="d-flex align-items-center ml-auto">
                    <select name="limit" class="custom-select mr-3" style="width: auto !important">
                        <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>15 Records</option>
                        <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>30 Records</option>
                        <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>50 Records</option>
                        <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>100 Records</option>
                    </select>

                    <select name="order" class="custom-select mr-3" style="width: auto !important">
                        <option value="">Latest First</option>
                        <option value="past" @if($r->get('order') == 'past'){{ __('selected') }}@endif>Past Dates First</option>
                    </select>

                    <button class="btn btn-default shadow-none">Go</button>
                </div>
            </div>
        </form>

        <table class="table">
            <tr class="card-header">
                <th>Site</th>
                <th>Staff Name</th>
                <th>Phone No</th>
                <th>Company</th>
                <th>Date</th>
                <th>Checked In</th>
                <th>Checked Out</th>
                <th>Vehicle</th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="6">
                    <p class="my-0">
                        No staff checkin has been captured that matches the selected options
                    </p>
                </td>
            </tr>

            @else

            @foreach ($result->items as $checkin)
            <tr>
                <td>{{ $checkin->site->name }}</td>
                {{-- <td><a href="{{ route('admin.visitors.single', $checkin->visitor->id) }}">{{ $checkin->visitor->name }}</a></td> --}}
                <td><a href="{{ route('admin.sites.staff', ['site_id' => $checkin->staff->company->site_id, 'company_id' => $checkin->staff->company->id, 'staff_id' => $checkin->staff->id]) }}">{{ $checkin->staff->name }}</a></td>
                <td>{{ $checkin->staff->phone }}</td>
                <td>{{ $checkin->staff->company->name }}</td>
                <td>{{ $checkin->fmtDate }}</td>
                <td>{{ $checkin->check_in }}</td>
                <td>{{ $checkin->check_out }}</td>
                <td>{{ $checkin->car_registration ? $checkin->car_registration:'None' }}</td>
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
