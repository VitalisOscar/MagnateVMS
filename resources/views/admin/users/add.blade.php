@extends('admin.root')

@section('title', 'Add a new User')

@section('page_heading', 'Add User')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.users') }}"class="breadcrumb-item">Users</a>
    <a class="breadcrumb-item active">Add New</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">User Details</h4>
        <a href="{{ route('admin.users') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Back to Users</a>
    </div>

    <div class="row">
        <form action="{{ route('admin.users.add') }}" autocomplete="off" method="post" class="col-md-9 col-lg-8">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for=""><strong>Full Name:</strong></label>
                    </div>

                    <div class="col-md-8">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input value="{{ old('name') }}" type="text" class="form-control" placeholder="e.g John Doe" name="name">
                        </div>
                        @if($errors->has('name'))
                        <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                        @else
                        <small>Enter the name of the user</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for=""><strong>Email:</strong></label>
                    </div>

                    <div class="col-md-8">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>
                            <input value="{{ old('email') }}" type="email" class="form-control" placeholder="e.g john@gmail.com" name="email">
                        </div>
                        @if($errors->has('email'))
                        <small class="text-danger">{{ $errors->get('email')[0] }}</small>
                        @else
                        <small>Email will be used when logging into the app</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for=""><strong>Password:</strong></label>
                    </div>

                    <div class="col-md-8">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input value="{{ old('password') }}" type="text" class="form-control" placeholder="" name="password">
                        </div>
                        @if($errors->has('password'))
                        <small class="text-danger">{{ $errors->get('password')[0] }}</small>
                        @else
                        <small>Set a password that the user will use to access account</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4"></div>
                <div class="col-md-8">
                    <button class="btn btn-success btn-block shadow-none">Save User</button>
                </div>
            </div>
        </form>

        <div class="col-md-3 col-lg-4">
            <p class="my-0">
                Users added on this page can use the username and password to log into the app.
                Use this form to create accounts for guards.
                The user can change their password later through the app, or you can do it if it needs to be reset
            </p>
        </div>
    </div>

</div>

@endsection
