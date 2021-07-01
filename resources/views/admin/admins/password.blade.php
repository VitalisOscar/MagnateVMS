<?php $user = auth('staff')->user(); ?>

@extends('admin.base')

@section('title', 'Change your password')

@section('page_heading')
<i class="fa fa-lock text-success mr-3" style="font-size: .8em"></i>
Change your Password
@endsection

@section('content')

<style>
    .form-control{
        background: #eee;
        border-color: #eee;
    }
</style>

<div class="card mx-auto" style="max-width: 400px">
    <div class="card-header py-2 bg-primary d-flex align-items-center">
        <h3 class="card-title mb-0 text-white">Set a new Password</h3>
    </div>

    <form method="post" action="{{ route('admin.staff.password') }}" autocomplete="off">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <label class="mb-0"><strong>Current Password:</strong></label>
                <input type="password" name="password" class="form-control" placeholder="" value="{{ old('password') }}" required>
                @if($errors->has('password'))
                <small class="text-danger">
                    {{ $errors->get('password')[0] }}
                </small>
                @else
                <small>This is to let us know you are the one making the change</small>
                @endif
            </div>

            <div class="form-group">
                <label class="mb-0"><strong>New Password:</strong></label>
                <input type="password" name="new_password" class="form-control" placeholder="" value="{{ old('new_password') }}" required>
                @if($errors->has('new_password'))
                <small class="text-danger">
                    {{ $errors->get('new_password')[0] }}
                </small>
                @else
                <small>Enter the new password you'd like to be using</small>
                @endif
            </div>

            <div class="form-group">
                <label class="mb-0"><strong>Confirm Password:</strong></label>
                <input type="password" name="confirm_password" class="form-control" placeholder="" value="{{ old('new_password') }}" required>
                @if($errors->has('confirm_password'))
                <small class="text-danger">
                    {{ $errors->get('confirm_password')[0] }}
                </small>
                @else
                <small>Enter the new password again</small>
                @endif
            </div>
        </div>

        <div class="py-3 px-3 border-top text-center">
            <button type="submit" class="btn btn-success shadow-none">Save Changes</button>
        </div>
    </form>

</div>

@endsection
