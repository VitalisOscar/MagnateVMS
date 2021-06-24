@extends('admin.root')

@section('title', 'Add a staff member at company')

@section('page_heading', 'Add a Staff Member')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}" class="breadcrumb-item">Sites</a>
    <a href="{{ route('admin.sites.single', ['site_id' => $company->site->id]) }}" class="breadcrumb-item active">{{ $company->site->name }}</a>
    <a href="{{ route('admin.sites.company', ['site_id' => $company->site->id, 'company_id' => $company->id]) }}" class="breadcrumb-item">{{ $company->name }}</a>
    <a class="breadcrumb-item active">Add Staff</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Staff Details</h4>
        <a href="{{ route('admin.sites.company', ['site_id' => $company->site->id, 'company_id' => $company->id]) }}" class="ml-auto btn btn-primary btn-sm shadow-none">Back to Company</a>
    </div>

    <div class="row">
        <div class="col-md-9 col-lg-8">
            <form action="{{ route('admin.sites.staff.add', ['site_id' => $company->site->id, 'company_id' => $company->id]) }}" method="post">
                @csrf
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-4">
                            <label for=""><strong>Staff Name:</strong></label>
                        </div>

                        <div class="col-md-8">
                            <div class="input-group input-group-alternative border shadow-none">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-user-circle"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ old('name') }}" placeholder="e.g Alice Brown" name="name">
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
                                <input type="tel" class="form-control" value="{{ old('phone') }}" maxlength="10" minlength="10" placeholder="e.g 0700123456" name="phone">
                            </div>
                            @if($errors->has('phone'))
                            <small class="text-danger">{{ $errors->get('phone')[0] }}</small>
                            @else
                            <small>Will be used to notify the staff if they have a visitor. Do not include country code</small>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <button class="btn btn-success btn-block shadow-none">Save Details</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-3 col-lg-4">
            <p class="my-0">
                You are adding a new staff member at <strong style="font-weight: 600">{{ $company->name }}</strong>.
                Added staff members can be selected by the guards as host staff when visitors are checking in.
                They can also be checked in and out by guards at sites where staff are set to be tracked
            </p>
        </div>
    </div>

    <hr class="my-4">

    <h4 class="font-weight-600">Import From File</h4>
    <p class="mt-0">
        Import multiple staff members from an excel or csv file. Ensure that the file has columns labelled 'Name' and 'Phone'. The headings should be on the first row
    </p>

    <form action="{{ route('admin.imports.staff', ['site_id' => $company->site->id, 'company_id' => $company->id]) }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="import" class="form-control-file mb-3" required>

        <button class="btn btn-white shadow-sm"><i class="fa fa-upload"></i> Import</button>
    </form>

</div>

@endsection
