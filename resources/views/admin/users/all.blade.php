@extends('admin.root')

@section('title', 'Add and Manage Users')

@section('page_heading', 'User Accounts')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">Users</a>
</div>

<div class="p-0 bg-white border rounded">

    @php
        $r = request();
    @endphp

    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Existing Users {{ '('.$result->total.' total)' }}</h4>
            <a href="{{ route('admin.users.add') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add User</a>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
            <select name="limit" class="custom-select mr-3" style="width: auto !important">
                <option value="15" @if($r->get('limit') == 15){{ __('selected') }}@endif>Upto 15 Records</option>
                <option value="30" @if($r->get('limit') == 30){{ __('selected') }}@endif>Upto 30 Records</option>
                <option value="50" @if($r->get('limit') == 50){{ __('selected') }}@endif>Upto 50 Records</option>
                <option value="100" @if($r->get('limit') == 100){{ __('selected') }}@endif>Upto 100 Records</option>
            </select>

            <select name="order" class="custom-select mr-3" style="width: auto !important">
                <option value="">Sort by Default</option>
                <option value="recent" @if($r->get('order') == 'recent'){{ __('selected') }}@endif>Added Recently</option>
                <option value="az" @if($r->get('order') == 'az'){{ __('selected') }}@endif>Name (A-Z)</option>
                <option value="za" @if($r->get('order') == 'za'){{ __('selected') }}@endif>Name (Z-A)</option>
            </select>

            <button class="btn btn-default shadow-none">Go</button>
        </form>

        <table class="table mb-0">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Name</th>
                <th>Username</th>
                <th>Date Added</th>
                <th>Last Login</th>
                <th></th>
            </tr>

            @if($result->isEmpty())
            <tr>
                <td colspan="5">
                    <p class="my-0">
                        There are no users who can log into the app. Once users are added, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 0;
            @endphp
            @foreach ($result->items as $user)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>
                    <a href="{{ route('admin.users.single', $user->username) }}">{{ $user->name }}</a>
                </td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->time }}</td>
                <td>
                    {!! $user->last_login ? $user->last_login->time.'<br>'.$user->last_login->site->name:'No Logins Yet' !!}
                </td>
                <td>
                    <a href="{{ route('admin.users.single', $user->username) }}">View User&nbsp;<i class="fa fa-share"></i></a>
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
