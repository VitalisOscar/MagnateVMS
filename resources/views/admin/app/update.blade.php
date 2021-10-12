@extends('admin.root')

@section('title', 'App Update')

@section('page_heading', 'App Update')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.app.versions') }}"class="breadcrumb-item">App Versions</a>
    <a class="breadcrumb-item active">Add New</a>
</div>

<div class="p-4 bg-white shadow-sm rounded ">

    <div class="border-bottom pb-3 mb-4 d-flex align-items-center">
        <h4 class="font-weight-600 mb-0">Version and File</h4>
    </div>

    <div class="row">
        <form action="{{ route('admin.app.versions.update') }}" enctype="multipart/form-data" method="post" class="col-md-12">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>App Version:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <div class="input-group input-group-alternative border shadow-none">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-android"></i>
                                </span>
                            </div>
                            <input value="{{ old('version') }}" type="text" class="form-control" placeholder="e.g 2021.10.1" name="version">
                        </div>
                        @if($errors->has('version'))
                        <small class="text-danger">{{ $errors->get('version')[0] }}</small>
                        @else
                        <small>Enter the app's version</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-4 col-lg-3">
                        <label for=""><strong>Apk File:</strong></label>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        <input type="file" class="form-control-file" name="apk_file" required>
                        @if($errors->has('apk_file'))
                        <small class="text-danger">{{ $errors->get('apk_file')[0] }}</small>
                        @else
                        <small>Select the installable apk file</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-success shadow-none">Save Version</button>
            </div>
        </form>
    </div>

</div>

@endsection
