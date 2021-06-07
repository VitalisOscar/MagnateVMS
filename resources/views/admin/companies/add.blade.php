@extends('admin.root')

@section('title', 'Add a company at site')

@section('page_heading', 'Add Company at Site')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}" class="breadcrumb-item">Sites</a>
    <a href="{{ route('admin.sites.single', ['site_id' => $site->id]) }}" class="breadcrumb-item active">{{ $site->name }}</a>
    <a class="breadcrumb-item active">Add Company</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Company Details</h4>
        <a href="{{ route('admin.sites.single', $site->id) }}" class="ml-auto btn btn-primary btn-sm shadow-none">Back to Site</a>
    </div>

    <div class="row">
        <div class="col-md-9 col-lg-8">
            <form action="{{ route('admin.sites.company.add', $site->id) }}" method="post">
                @csrf
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-4">
                            <label for=""><strong>Company Name:</strong></label>
                        </div>

                        <div class="col-md-8">
                            <div class="input-group input-group-alternative border shadow-none">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-building-o"></i>
                                    </span>
                                </div>
                                <input type="text" value="{{ old('name') }}" class="form-control" placeholder="e.g MVL" name="name">
                            </div>
                            @if($errors->has('name'))
                            <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                            @else
                            <small>Enter the name of the company at the site</small>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <button class="btn btn-success btn-block shadow-none">Save Company</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-3 col-lg-4">
            <p class="my-0">
                You are adding a company at the site <strong style="font-weight: 600">{{ $site->name }}</strong>.
                After adding a company, you can add staff members who work there
            </p>
        </div>
    </div>

</div>

@endsection
