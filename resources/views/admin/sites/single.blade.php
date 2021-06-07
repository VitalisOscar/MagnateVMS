@extends('admin.root')

@section('title', 'Manage Site - '.$site->name)

@section('page_heading', 'Manage Site')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <a href="{{ route('admin.sites') }}" class="breadcrumb-item">Sites</a>
    <a class="breadcrumb-item active">{{ $site->name }}</a>
</div>

<div class="bg-white border rounded mb-4">

    @php
        $r = request();
    @endphp

    <div class="">

        <div class="px-4 py-3 d-flex align-items-center">
            <h4 class="font-weight-600 mb-0">Companies at Site {{ '('.count($site->companies).')' }}</h4>
            <a href="{{ route('admin.sites.company.add', $site->id) }}" class="ml-auto btn btn-primary btn-sm shadow-none">Add Company</a>
        </div>

        <table class="table mb-0">
            <tr class="card-header">
                <th class="text-center">#</th>
                <th>Company Name</th>
                <th>Total Staff</th>
                <th></th>
            </tr>

            @if(count($site->companies) == 0)
            <tr>
                <td colspan="4">
                    <p class="my-0">
                        There are no companies added at this site. Once companies are added, they'll appear here
                    </p>
                </td>
            </tr>
            @else

            @php
                $i = 1;
            @endphp
            @foreach ($site->companies as $company)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td>
                    <a href="{{ route('admin.sites.company', ['company_id' => $company->id, 'site_id' => $site->id]) }}">{{ $company->name }}</a>
                </td>
                <td>{{ $company->staff_count }}</td>
                <td>
                    <a class="mr-3" href="{{ route('admin.sites.company', ['company_id' => $company->id, 'site_id' => $site->id]) }}">View&nbsp;<i class="fa fa-share"></i></a>
                    <a href="">Delete&nbsp;<i class="fa fa-trash"></i></a>
                </td>
            </tr>

            @php
                $i++;
            @endphp
            @endforeach

            @endif
        </table>

    </div>

</div>

<h4 class="font-weight-600">Site Settings</h4>

<div class="bg-white border rounded mb-4 p-4" id="options">

    <form action="{{ route('admin.sites.update', $site->id) }}" method="post">
        @csrf

        <div class="form-group">
            <h5 class="font-weight-600">Site Name</h5>

            <div class="form-row">

                <div class="col-12">
                    <div class="input-group input-group-alternative border shadow-none">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-map-marker"></i>
                            </span>
                        </div>
                        <input value="{{ old('name') != null ? old('name'):$site->name }}" type="text" class="form-control" placeholder="e.g Magnate Centre" name="name" required>
                    </div>
                    @if($errors->has('name'))
                    <small class="text-danger">{{ $errors->get('name')[0] }}</small>
                    @else
                    <p class="my-0">Enter the name of the site or its address</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group mb-4">
            <h5 class="font-weight-600">Logins</h5>
            <p class="mt-0 mb-3">
                Disabling logins will mean app users at {{ $site->name }} will not be able to sign in and track visitors, staff or vehicles leaving or coming in.
                Logins are currently enabled. Toggle the switch and save to disable
            </p>

            <div class="px-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" id="logins" name="logins" class="custom-control-input" @if($site->loginsAreEnabled()){{ __('checked') }}@endif>
                    <label for="logins" class="custom-control-label ml-4" style="font-size: .95em">
                        <span class="top-0">Allow logins</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <h5 class="font-weight-600">Data Collection</h5>
            <p class="mt-0 mb-3">
                Please select what is to be tracked by the guards at the site
            </p>

            <div class="row">
                @foreach(\App\Models\Site::TRACKABLES as $k => $trackable)
                <div class="col-md-6 col-lg-4 mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="track_{{ $k }}" name="track_{{ $k }}" class="custom-control-input" @if($site->tracks($k)){{ __('checked') }}@endif>
                        <label for="track_{{ $k }}" class="custom-control-label" style="font-size: .95em">
                            <span class="top-0">{{ $trackable }}</span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <button class="btn btn-primary shadow-none">Save Changes</button>
        </div>

    </form>

</div>

@endsection
