@extends('admin.root')

@section('title', 'Manage User - '.$user->name)

@section('page_heading', 'Manage User')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.users') }}"class="breadcrumb-item">Users</a>
    <a class="breadcrumb-item active">{{ $user->name }}</a>
</div>

<div class="bg-white border rounded mb-4 p-4">


    <div class="mb-3 d-flex align-items-center">
        <h4 class="font-weight-600">Account Info</h4>
        <form id="delete_user_form" class="d-inline-block ml-auto" method="post" action="{{ route('admin.users.delete', $user->id) }}">
            @csrf
            <button class="btn btn-danger btn-sm shadow-none">Delete User</button>
        </form>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="post">
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
                        <input value="{{ old('name') != null ? old('name'):$user->name }}" type="text" class="form-control" placeholder="e.g John Doe" name="name">
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
                                <i class="fa fa-user"></i>
                            </span>
                        </div>
                        <input type="text" value="{{ old('email') != null ? old('email'):$user->email }}" class="form-control" placeholder="e.g john_doe" name="email">
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
                        <input type="password" value="{{ old('password') }}" class="form-control" placeholder="" name="password">
                    </div>
                    @if($errors->has('password'))
                    <small class="text-danger">{{ $errors->get('password')[0] }}</small>
                    @else
                    <small>Please leave the field blank unless you are resetting the password</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="text-right">
            <button class="btn btn-primary shadow-none">Save Changes</button>
        </div>

    </form>

</div>

@endsection

@section('scripts')
<script>
document.querySelector('#delete_user_form').addEventListener('submit', function(evt){
    evt.preventDefault();

    if(confirm("Are you sure you want to delete the account for {{ $user->name }}")){
        document.querySelector('#delete_user_form').submit();
    }
});
</script>
@endsection
