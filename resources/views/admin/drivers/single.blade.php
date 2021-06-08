@extends('admin.root')

@section('title', 'Driver Detail - '.$driver->name.' ('.$driver->department.' Department)')

@section('page_heading', 'Driver Overview')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.vehicles.drivers')}}" class="breadcrumb-item active">Drivers</a>
    <a class="breadcrumb-item active">{{ $driver->name }}</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Driver Details</h4>
        <a href="{{ route('admin.vehicles.drivers') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Back to Drivers</a>
    </div>

    <div class="row">
        <form action="{{ route('admin.vehicles.drivers.update', $driver->id) }}" method="post" class="col-md-12">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>Name:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input value="{{ old('name') ? old('name'):$driver->name }}" type="text" class="form-control" placeholder="e.g James Maina" name="name">
                        </div>
                        @if($errors->has('name'))
                        <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                        @else
                        <small>Enter the driver's name</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>Phone:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                            <input value="{{ old('phone') ? old('phone'):$driver->phone }}" type="text" class="form-control" placeholder="e.g 0700123456" name="phone">
                        </div>
                        @if($errors->has('department'))
                        <small class="text-danger">{{ $errors->get('phone')[0] }}</small>
                        @else
                        <small>Enter the driver's phone number</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>Department:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-book"></i>
                                </span>
                            </div>
                            <input value="{{ old('department') ? old('department'):$driver->department }}" type="text" class="form-control" placeholder="e.g Sales" name="department">
                        </div>
                        @if($errors->has('department'))
                        <small class="text-danger">{{ $errors->get('department')[0] }}</small>
                        @else
                        <small>Enter the department the driver is attached to</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-success shadow-none">Save Changes</button>
            </div>
        </form>
    </div>

</div>

@endsection
