@extends('admin.root')

@section('title', 'Manage Company - '.$company->name.' (at site '.$company->site->name.')')

@section('page_heading', 'Manage Company')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}" class="breadcrumb-item">Sites</a>
    <a href="{{ route('admin.sites.single', ['site_id' => $company->site->id]) }}" class="breadcrumb-item active">{{ $company->site->name }}</a>
    <a class="breadcrumb-item active">{{ $company->name }}</a>
</div>

<div class="bg-white border rounded mb-4">

    @php
        $r = request();
    @endphp

    <div class="">

        <div>

            <div class="px-4 py-3 d-flex align-items-center">
                <h4 class="font-weight-600 mb-0">Staff Members ({{ $result->total.' Total' }})</h4>

                <a href="{{ route('admin.sites.staff.add', ['site_id' => $company->site->id, 'company_id' => $company->id]) }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add New</a>
            </div>

            <?php $r = request(); ?>

            <form class="px-4 pb-3 d-flex align-items-center">
                <input type="search" name="keyword" class="form-control bg-white mr-3" placeholder="Seach by name, phone, extension..." value="{{ $r->filled('keyword') ? $r->get('keyword'):'' }}">

                <div class="d-flex align-items-center ml-auto">
                    <select name="limit" class="custom-select mr-3" style="width: auto !important">
                        <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                        <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                        <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                        <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
                    </select>

                    <button class="btn btn-default shadow-none">Search</button>
                </div>
            </form>

        </div>

        <table class="table">
            <tr class="card-header">
                <th style="text-align: center">#</th>
                <th>Staff Name</th>
                <th>Department</th>
                <th>Phone No</th>
                <th>Extension</th>
                <th></th>
            </tr>

            @if($company->staff_count == 0)
            <tr>
                <td colspan="6">
                    <p class="my-0">
                        There are no staff added at this site. Once they are added by a privilleged user, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 0;
            @endphp
            @foreach ($result->items as $staff)
            <tr>
                <td style="text-align: center">{{ $result->from + $i }}</td>
                <td>
                    <a href="{{ route('admin.sites.staff', ['staff_id' => $staff->id, 'company_id' => $company->id, 'site_id' => $company->site->id]) }}">{{ $staff->name }}</a>
                </td>
                <td>{{ $staff->department }}</td>
                <td>{{ $staff->phone }}</td>
                <td>{{ $staff->extension }}</td>
                <td>
                    <a class="mr-3" href="{{ route('admin.sites.staff', ['staff_id' => $staff->id, 'company_id' => $company->id, 'site_id' => $company->site->id]) }}">View&nbsp;<i class="fa fa-share"></i></a>

                    <form method="post" action="{{ route('admin.sites.staff.delete', ['staff_id' => $staff->id, 'company_id' => $company->id, 'site_id' => $company->site->id]) }}" class="d-inline-block">
                        @csrf
                        <button class="btn btn-link p-0" style="text-transform: none">Delete&nbsp;<i class="fa fa-trash"></i></button>
                    </form>
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
