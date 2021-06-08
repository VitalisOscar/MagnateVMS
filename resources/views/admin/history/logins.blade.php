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
            <h4 class="font-weight-600 mb-0">All Logins</h4>
            <a href="" class="ml-auto btn btn-primary btn-sm shadow-none">View All</a>
        </div>

        <form class="px-4 pb-3 d-flex align-items-center">
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
                <th>Time</th>
                <th>Site</th>
                <th>Outcome</th>
            </tr>

            @if ($result->total == 0)
            <tr>
                <td colspan="6">
                    <p class="my-0">
                        There are no logins at the moment
                    </p>
                </td>
            </tr>
            @endif

            <?php $i = 0; ?>

            @foreach ($result->items as $login)
            <tr>
                <td class="text-center">{{ $result->from + $i }}</td>
                <td>{{ \Illuminate\Support\Str::title($login->type) }}</td>
                <td>{{ $login->user->name }}</td>
                <td>{{ $login->time }}</td>
                <td>{{ $login->site_id ? $login->site->name:'-' }}</td>
                <td>{{ \Illuminate\Support\Str::title($login->status) }}</td>
            </tr>
            <?php $i++; ?>
            @endforeach
        </table>

    </div>
</div>

@endsection
