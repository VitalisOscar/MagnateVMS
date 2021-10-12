@extends('admin.root')

@section('title', 'App Versions')

@section('page_heading', 'App Versions')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">App Versions</a>
</div>

<div class="p-0 bg-white border rounded">

    @php
        $r = request();
    @endphp

    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">App Versions {{ '('.$result->total.' total)' }}</h4>
            <a href="{{ route('admin.app.versions.update') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add New</a>
        </div>

        <table class="table mb-0">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Version</th>
                <th>Added On</th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="3">
                    <p class="my-0">
                        App versions will appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 0;
            @endphp
            @foreach ($result->items as $update)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>{{ $update->version }}</td>
                <td>{{ $update->date }}</td>
            </tr>

            @php
                $i++;
            @endphp
            @endforeach

            <tr>
                <td colspan="3">
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
