@extends('admin.root')

@section('title', 'Add and Manage Sites')

@section('page_heading', 'All Sites')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">Sites</a>
</div>

<div class="p-0 bg-white border rounded">

    @php
        $r = request();
    @endphp

    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Sites {{ '('.$result->total.' total)' }}</h4>
            <a href="{{ route('admin.sites.add') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add Site</a>
        </div>

        <table class="table mb-0">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Name</th>
                <th>Companies</th>
                <th>Total Staff</th>
                <th></th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="4">
                    <p class="my-0">
                        There are no sites at the moment. Once sites are added, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 0;
            @endphp
            @foreach ($result->items as $site)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>
                    <a href="{{ route('admin.sites.single', $site->id) }}">{{ $site->name }}</a>
                </td>
                <td>{{ $site->companies_count }}</td>
                <td>{{ $site->staff_count }}</td>
                <td>
                    <a href="{{ route('admin.sites.single', $site->id) }}">View Site&nbsp;<i class="fa fa-share"></i></a>
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
