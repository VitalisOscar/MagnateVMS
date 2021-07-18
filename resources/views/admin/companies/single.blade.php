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

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Staff Members</h4>
            <a href="{{ route('admin.sites.staff.add', ['site_id' => $company->site->id, 'company_id' => $company->id]) }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add New</a>
        </div>

        <table class="table">
            <tr class="card-header">
                <th style="text-align: center">#</th>
                <th>Staff Name</th>
                <th>Phone No</th>
                <th></th>
            </tr>

            @if($company->staff_count == 0)
            <tr>
                <td colspan="4">
                    <p class="my-0">
                        There are no staff added at this site. Once they are added by a privilleged user, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 1;
            @endphp
            @foreach ($result->items as $staff)
            <tr>
                <td style="text-align: center">{{ $i }}</td>
                <td>
                    <a href="{{ route('admin.sites.staff', ['staff_id' => $staff->id, 'company_id' => $company->id, 'site_id' => $company->site->id]) }}">{{ $staff->name }}</a>
                </td>
                <td>{{ $staff->phone }}</td>
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

            @php
                $route = \Illuminate\Support\Facades\Route::current();
                $prev = array_merge($route->parameters, $r->except('page'), ['page' => $result->prev_page]);
                $next = array_merge($route->parameters, $r->except('page'), ['page' => $result->next_page]);
            @endphp

            <tr>
                <td colspan="5">
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
