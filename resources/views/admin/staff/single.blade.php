@extends('admin.root')

@section('title', 'Staff Member - '.$staff->name.' ('.$staff->company->name.')')

@section('page_heading', 'Staff Member')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}" class="breadcrumb-item">Sites</a>
    <a href="{{ route('admin.sites.single', ['site_id' => $staff->company->site->id]) }}" class="breadcrumb-item active">{{ $staff->company->site->name }}</a>
    <a href="{{ route('admin.sites.company', ['site_id' => $staff->company->site->id, 'company_id' => $staff->company->id]) }}" class="breadcrumb-item">{{ $staff->company->name }}</a>
    <a class="breadcrumb-item active">{{ $staff->name }}</a>
</div>

<div class="bg-white border rounded mb-4" id="options">

    <div class="card-header">
        <h4 class="font-weight-600 mb-0 card-title">Staff Info</h4>
    </div>

    <form action="{{ route('admin.sites.staff.update', ['staff_id' => $staff->id, 'company_id' => $staff->company->id, 'site_id' => $staff->company->site->id]) }}" method="post">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for=""><strong>Name:</strong></label>
                    </div>

                    <div class="col-md-8">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input value="{{ old('name') != null ? old('name'):$staff->name }}" type="text" class="form-control" placeholder="e.g John Doe" name="name">
                        </div>
                        @if($errors->has('name'))
                        <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                        @else
                        <small>Enter the name of the staff member</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4">
                        <label for=""><strong>Phone Number:</strong></label>
                    </div>

                    <div class="col-md-8">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                            <input type="text" value="{{ old('phone') != null ? old('phone'):$staff->phone }}" minlength="10" maxlength="10" class="form-control" placeholder="e.g 0700123456" name="phone">
                        </div>
                        @if($errors->has('phone'))
                        <small class="text-danger">{{ $errors->get('phone')[0] }}</small>
                        @else
                        <small>We shall beep the staff when a visitor comes to see them</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-primary shadow-none">Save Changes</button>
            </div>

        </div>

    </form>

</div>

@endsection
