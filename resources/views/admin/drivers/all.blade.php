@extends('admin.root')

@section('title', 'Add and Manage Drivers')

@section('page_heading', 'Drivers')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">Drivers</a>
</div>

<div class="p-0 bg-white border rounded">

    @php
        $r = request();
    @endphp

    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Company Drivers</h4>
            <a href="{{ route('admin.vehicles.drivers.add') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add New</a>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <select name="limit" class="custom-select mr-3" style="width: auto !important">
                <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
            </select>

            <button class="btn btn-default shadow-none">Go</button>
        </form>

        <table class="table mb-0">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Department</th>
                <th></th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="5">
                    <p class="my-0">
                        Once drivers are added to the system, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 0;
            @endphp
            @foreach ($result->items as $driver)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>
                    <a href="{{ route('admin.vehicles.drivers.single', $driver->id) }}">{{ $driver->name }}</a>
                </td>
                <td>{{ $driver->phone }}</td>
                <td>{{ $driver->department }}</td>
                <td>
                    <a href="{{ route('admin.vehicles.drivers.single', $driver->id) }}">View Driver&nbsp;<i class="fa fa-share"></i></a>
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
