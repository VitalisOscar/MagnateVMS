<?php $user = auth('staff')->user(); ?>

@extends('admin.base')

@section('title', 'Create a staff account')

@section('page_heading')
<i class="fa fa-plus text-success mr-3" style="font-size: .8em"></i>
Create an Account
@endsection

@section('content')

<style>
    .form-control{
        background: #eee;
        border-color: #eee;
    }
</style>

<div class="card mx-auto" style="max-width: 500px">
    <div class="card-header py-2 bg-primary d-flex align-items-center">
        <h3 class="card-title mb-0 text-white">Add a new Account</h3>
    </div>

    <form method="post" action="{{ route('admin.staff.add') }}">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <label class="mb-0"><strong>Staff Name:</strong></label>
                <input type="text" name="name" class="form-control" placeholder="e.g John Doe" value="{{ old('name') }}" required>
                @if($errors->has('name'))
                <small class="text-danger">
                    {{ $errors->get('name')[0] }}
                </small>
                @else
                <small>Enter the name of the staff member who'll be using this account</small>
                @endif
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="mb-0"><strong>Username:</strong></label>
                            <input type="text" name="username" class="form-control" placeholder="e.g johndoe" value="{{ old('username') }}" required>
                            @if($errors->has('username'))
                            <small class="text-danger">
                                {{ $errors->get('username')[0] }}
                            </small>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="mb-0"><strong>Password:</strong></label>
                            <input type="password" name="password" class="form-control" placeholder="Password" value="{{ old('password') }}" required>
                            @if($errors->has('password'))
                            <small class="text-danger">
                                {{ $errors->get('password')[0] }}
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                <small>The username and password will be used for logins</small>
            </div>

            <div class="form-group mb-0">

                <label><strong>Account Type:</strong></label>
                <div class="d-flex">
                    <div class="mr-5">
                        <div class="custom-control custom-radio">
                            <input type="radio" name="role" class="custom-control-input" id="role_regular" value="{{ \App\Models\Staff::ROLE_STAFF }}" @if(old('role') == \App\Models\Staff::ROLE_STAFF) checked @endif>
                            <label class="custom-control-label" for="role_regular">Regular Account</label>
                        </div>
                    </div>

                    <div>
                        <div class="custom-control custom-radio">
                            <input type="radio" name="role" class="custom-control-input" id="role_admin" value="{{ \App\Models\Staff::ROLE_ADMIN }}" @if(old('role') == \App\Models\Staff::ROLE_ADMIN) checked @endif>
                            <label class="custom-control-label" for="role_admin">Administrator Account</label>
                        </div>
                    </div>
                </div>
                <small>An administrator account can add, change or remove accounts, categories, screens and packages</small>
                @if($errors->has('role'))
                <br><small class="text-danger">
                    {{ $errors->get('role')[0] }}
                </small>
                @endif
            </div>
        </div>

        <div class="py-3 px-3 border-top text-center">
            <button type="submit" class="btn btn-success shadow-none">Create Account</button>
        </div>
    </form>

</div>

@endsection
