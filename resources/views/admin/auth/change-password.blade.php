@extends('admin.root')

@section('title', 'Update your Password')

@section('page_heading', 'Update your Password')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a class="breadcrumb-item active">Update Password</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Update your Password</h4>
    </div>

    <div class="row">
        <form role="form" method="post" class="col-12">
            @csrf

            <div class="form-row">

                <div class="col-sm-6 col-lg-4">
                    <div class="form-group">
                        <label><strong>Current Password</strong></label>
                        <input type="password" name="password" value="{{ old('password') }}" class="form-control" placeholder="Current Password" required>
                        @if($errors->has('password'))
                        <small class="text-danger">
                            {{ $errors->get('password')[0] }}
                        </small>
                        @else
                        <small>Enter the password that you currently use</small>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="form-group">
                        <label><strong>New Password</strong></label>
                        <input type="password" name="new_password" value="{{ old('new_password') }}" class="form-control" placeholder="New Password" required>
                        @if($errors->has('new_password'))
                        <small class="text-danger">
                            {{ $errors->get('new_password')[0] }}
                        </small>
                        @else
                        <small>Enter the new password</small>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="form-group">
                        <label><strong>Confirm Password</strong></label>
                        <input type="password" name="confirm_password" value="{{ old('confirm_password') }}" class="form-control" placeholder="Confirm Password" required>
                        @if($errors->has('confirm_password'))
                        <small class="text-danger">
                            {{ $errors->get('confirm_password')[0] }}
                        </small>
                        @else
                        <small>Confirm new password</small>
                        @endif
                    </div>
                </div>

            </div>

            <div class="card-footer bg-white px-0 py-3">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

</div>

@endsection
