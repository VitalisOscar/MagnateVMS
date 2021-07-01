<?php $user = auth('staff')->user(); ?>

@extends('admin.base')

@section('title', 'Manage Staff Account for '.$staff->name)

@section('page_heading')
<i class="ni ni-single-02 text-success mr-3" style="font-size: .8em"></i>
Manage Account - {{ $staff->name }}
@endsection

@section('content')

<div class="row">

    <div class="col-md-6 mb-4">
        <div class="card mb-4 mx-auto" style="max-width: 400px">
            <div class="card-header bg-primary py-2">
                <h3 class="card-title mb-0 text-white">Account Details</h3>
            </div>

            <form method="post" action="{{ route('admin.staff.edit', $staff->username) }}">
                @csrf

                <div class="card-body">
                    <div class="form-group">
                        <label class="mb-0"><strong>Staff Name:</strong></label>
                        <input type="text" value="{{ old('name') != null ? old('name'):$staff->name }}" class="form-control" name="name">
                        @if($errors->has('name'))
                        <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                        @else
                        <small>Staff's name associated with this account</small>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="mb-0"><strong>Username:</strong></label>
                        <input type="text" name="username" value="{{ old('username') != null ? old('username'):$staff->username }}" class="form-control" placeholder="johndoe_001" required>
                        @if($errors->has('username'))
                        <small class="text-danger">{{ $errors->get('username')[0] }}</small>
                        @else
                        <small>The username will be used during login</small>
                        @endif
                    </div>

                    <div class="form-group mb-0">

                        <label>Account Type</label>
                        <div class="d-flex">
                            <div class="mr-5">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="role" class="custom-control-input" id="role_regular" value="{{ \App\Models\Staff::ROLE_STAFF }}" @if($staff->role == \App\Models\Staff::ROLE_STAFF || old('role') == \App\Models\Staff::ROLE_STAFF) checked @endif>
                                    <label class="custom-control-label" for="role_regular">Regular Account</label>
                                </div>
                            </div>

                            <div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="role" class="custom-control-input" id="role_admin" value="{{ \App\Models\Staff::ROLE_ADMIN }}" @if($staff->role == \App\Models\Staff::ROLE_ADMIN || old('role') == \App\Models\Staff::ROLE_ADMIN) checked @endif>
                                    <label class="custom-control-label" for="role_admin">Administrator Account</label>
                                </div>
                            </div>
                        </div>
                        @if($errors->has('role'))
                        <small class="text-danger">{{ $errors->get('role')[0] }}</small>
                        @else
                        <small>An administrator account can add, change or remove accounts, categories and screens</small>
                        @endif
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer bg-white text-center py-3">
                    <button class="btn btn-success shadow-none">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-5">
            <h4>More Actions</h4>

            @if(isset($message))
            <script>
            alert('{{ $message }}');
            </script>
            @endif

            <div class="mb-2">Please execute this actions only if necessary. This actions are permanent</div>

            <div class="mb-4">
                <button class="btn btn-success shadow-none mr-3 mb-3" data-toggle="modal" data-target="#reset-password"><i class="fa fa-lock mr-2"></i>Reset Password</button>
                <button class="btn btn-danger shadow-none mb-3" data-toggle="modal" data-target="#delete-account"><i class="fa fa-trash mr-2"></i>Delete Account</button>
            </div>
        </div>
    </div>

</div>

<!-- Delete account modal-->
<div class="modal fade" id="delete-account">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0 bg-white d-flex align-items-center">
                <h4 class="modal-title text-primary mb-0 float-left">Delete Account?</h4>
                <span class="close float-right text-white" style="cursor: pointer" data-dismiss="modal"><i class="fa fa-times"></i></span>
                <div class="clearfix"></div>
            </div>

            <form method="post" action="{{ route('admin.staff.delete', $staff->username) }}">
                @csrf
                <div class="modal-body">
                    Proceed to delete this account ({{ $staff->name }}). Once deleted, the account will be gone and cannot be used to log into this system again
                </div>

                <div class="text-right border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-white shadow-none py-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger shadow-none py-2">Continue</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- reset password modal-->
<div class="modal fade" id="reset-password">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-header bg-primary py-3 d-flex align-items-center">
                <h4 class="modal-title text-white mb-0 float-left"><i class="fa fa-lock mr-3"></i>Set a new Password</h4>
                <span class="close float-right text-white" style="cursor: pointer" data-dismiss="modal"><i class="fa fa-times"></i></span>
                <div class="clearfix"></div>
            </div>

            <form method="post" action="{{ route('admin.staff.password.reset', ['username'=>$staff->username]) }}">
                @csrf
                <div class="modal-body">
                    Set a new login password for {{ $staff->name }}
                    <div class="form-group mt-3">
                        <label class="mb-0"><strong>New Password:</strong></label>
                        <input type="text" class="form-control" name="password" required>
                        <small>Enter the new password to be used</small>
                    </div>
                </div>

                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-white shadow-none py-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger shadow-none py-2">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
