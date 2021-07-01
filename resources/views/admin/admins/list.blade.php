<?php $user = auth('staff')->user(); ?>

@extends('admin.base')

@section('title', 'Manage Staff Accounts')

@section('page_heading')
<i class="ni ni-single-02 text-success mr-3" style="font-size: .8em"></i>
Staff Accounts
@endsection

@section('content')

<style>
    .form-control{
        background: #eee;
        border-color: #eee;
    }
</style>

<div class="card">
    <div class="card-header py-3 bg-white d-flex align-items-center">
        <?php $existing = count($staff); ?>
        <h3 class="card-title mb-0 text-primary">{{ $existing }} @if($existing == 1){{ __('Existing Account') }}@else{{ __('Existing Accounts') }}@endif </h3>
        <a href="{{ route('admin.staff.add') }}" class="btn btn-link p-0 ml-auto float-right"><i class="fa fa-plus mr-1"></i>Add New</a>
    </div>

    <div class="card-body border-bottom-0 p-0">
        <table class="table">
            <tr class="bg-primary text-white border-bottom-0">
                <th class="text-center" style="width: 60px">#</th>
                <th>Name</th>
                <th>Username</th>
                <th>Account Type</th>
                <th>Date Added</th>
                <th></th>
            </tr>
            @for($i=0; $i<count($staff); $i++)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ $staff[$i]->name }}</td>
                <td>{{ $staff[$i]->username }}</td>
                <td>
                    @if($staff[$i]->role == \App\Models\Staff::ROLE_ADMIN)
                    Administrator
                    @else
                    Regular
                    @endif
                </td>
                <td>{{ $staff[$i]->date }}</td>
                <td><a href="{{ route('admin.staff.single', [$staff[$i]->username]) }}">View Account</a></td>
            </tr>
            @endfor
        </table>
    </div>

</div>

@endsection
