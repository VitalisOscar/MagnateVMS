@extends('admin.root')

@section('title', 'Login History')

@section('page_heading', 'Login History')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">Logins</a>
</div>

<?php $r = request(); ?>

<div class="bg-white border rounded mb-4">
    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">User Logins</h4>


            <div class="dropdown ml-auto">
                <button class="ml-auto mr-0 btn btn-primary btn-sm shadow-none dropdown-toggle" data-toggle="dropdown">Export to Excel</button>
                <ul class="dropdown-menu" id="export">
                    <li class="dropdown-item" title="Export all records">
                        <a class="dropdown-link" href="{{ route('admin.exports.logins', ['filters' => 0]) }}">Export All</a>
                    </li>

                    <li class="dropdown-item" title="Add selected filters e.g date and sorting before exporting">
                        <a class="dropdown-link" href="{{ route('admin.exports.logins', array_merge($r->all(), ['filters' => 1])) }}">Add Selected Options</a>
                    </li>
                </ul>
            </div>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <input type="search" name="keyword" class="form-control bg-white mr-3" placeholder="Search User..." value="{{ $r->filled('keyword') ? $r->get('keyword'):'' }}">

            <input type="readonly" placeholder="Any Date" id="flatpickr" class="form-control flatpickr mr-3" name="date" value="{{ $dates ?? '' }}">

            <select name="site" class="custom-select mr-3" style="width: auto !important; max-width: 160px">
                <option value="">Any Site</option>
                @foreach(\App\Models\Site::all() as $site)
                <option value="{{ $site->id }}" @if($r->get('site') == $site->id){{ __('selected') }}@endif>{{ $site->name }}</option>
                @endforeach
            </select>

            <select name="limit" class="custom-select mr-3" style="width: auto !important">
                <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
            </select>

            <select name="order" class="custom-select mr-3" style="width: auto !important">
                <option value="recent" @if($r->get('order') == 'latest'){{ __('selected') }}@endif>Latest</option>
                <option value="oldest" @if($r->get('order') == 'oldest'){{ __('selected') }}@endif>Oldest</option>
            </select>

            <button class="btn btn-default shadow-none">Go</button>
        </form>

        <table class="table">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Type</th>
                <th>Account</th>
                <th>Date</th>
                <th>Time</th>
                <th>Site</th>
                <th>Outcome</th>
            </tr>

            @if ($result->total == 0)
            <tr>
                <td colspan="7">
                    <p class="my-0">
                        There are no logins at the moment
                    </p>
                </td>
            </tr>
            @else

            <?php $i = 0; ?>

            @foreach ($result->items as $login)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>{{ \Illuminate\Support\Str::title($login->user_type) }}</td>
                <td>{{ $login->user->name }}</td>
                <td>{{ $login->fmt_date }}</td>
                <td>{{ $login->fmt_time }}</td>
                <td>{{ $login->site_id ? $login->site->name:'-' }}</td>
                <td>{{ \Illuminate\Support\Str::title($login->status) }}</td>
            </tr>
            <?php $i++; ?>
            @endforeach

            <tr>
                <td colspan="7">
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
