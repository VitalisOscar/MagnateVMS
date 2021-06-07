@extends('admin.root')

@section('title', 'Add Company Vehicle')

@section('page_heading', 'Add Company Vehicle')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.vehicles') }}"class="breadcrumb-item">Vehicles</a>
    <a class="breadcrumb-item active">Add New</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Vehicle Details</h4>
        <a href="{{ route('admin.vehicles') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Back to Vehicles</a>
    </div>

    <div class="row">
        <form action="{{ route('admin.vehicles.add') }}" method="post" class="col-md-12">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>Registration Number:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-car"></i>
                                </span>
                            </div>
                            <input value="{{ old('registration_no') }}" type="text" class="form-control" placeholder="e.g KBD 000M" name="registration_no">
                        </div>
                        @if($errors->has('registration_no'))
                        <small class="text-danger">{{ $errors->get('registration_no')[0] }}</small>
                        @else
                        <small>Enter the vehicle's registration number</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>Description:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-car"></i>
                                </span>
                            </div>
                            <input value="{{ old('description') }}" type="text" class="form-control" placeholder="e.g White Pickup Truck" name="description">
                        </div>
                        @if($errors->has('description'))
                        <small class="text-danger">{{ $errors->get('description')[0] }}</small>
                        @else
                        <small>A short description of the vehicle e.g color, make etc</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-success shadow-none">Save</button>
            </div>
        </form>
    </div>

</div>

@endsection
