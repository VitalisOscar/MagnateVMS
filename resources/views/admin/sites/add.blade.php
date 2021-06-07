@extends('admin.root')

@section('title', 'Add a new Site')

@section('page_heading', 'Add Site')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}" class="breadcrumb-item">Sites</a>
    <a class="breadcrumb-item active">Add Site</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Site Details</h4>
        <a href="{{ route('admin.sites') }}" class="ml-auto btn btn-primary btn-sm shadow-none">Back to Sites</a>
    </div>

    <div class="row">
        <div class="col-md-9 col-lg-8">
            <form action="{{ route('admin.sites.add') }}" method="post">
                @csrf
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-4">
                            <label for=""><strong>Site Name:</strong></label>
                        </div>

                        <div class="col-md-8">
                            <div class="input-group input-group-alternative border shadow-none">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" placeholder="e.g Mombasa Road" name="name">
                            </div>
                            @if($errors->has('name'))
                            <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                            @else
                            <small>Enter the name of the site</small>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <button class="btn btn-success btn-block shadow-none">Save Site</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-3 col-lg-4">
            <p class="my-0">
                Sites are the physical locations where visitors, staff and vehicles entering and leaving need to be tracked
            </p>
        </div>
    </div>

</div>

@endsection
