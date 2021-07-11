@extends('admin.root')

@section('title', 'Dashboard')

@section('page_heading', 'Welcome')

@section('content')

<div class="breadcrumb bg-transparent p-0">
    <a class="breadcrumb-item active">Dashboard</a>
</div>

<div class="card mb-4">
    <div class="card-body">

        <h4 class="mb-4 font-weight-800">Welcome {{ auth('admin')->user()->name }}</h4>

        <div class="row">
            <div class="col-sm-6 col-md-4 col-lg-6 col-xl-4 mb-4 mb-xl-0">
                <div class="p-3 border rounded d-sm-flex">
                    <span class="mb-3 mb-sm-0 bg-success text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="font-size: 1.2em; min-width: 40px; height: 40px">
                        <i class="fa fa-building"></i>
                    </span>

                    <div class="ml-sm-3 w-100">
                        <h4 class="font-weight-700">{{ $totals['sites'].' Sites' }}</h4>
                        <p class="mb-3">
                            Physical sites where activity is to be tracked
                        </p>

                        <div>
                            <a href="{{ route('admin.sites') }}" class="btn shadow-none btn-sm btn-block btn-success">
                                View Sites
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4 col-lg-6 col-xl-4 mb-4 mb-xl-0">
                <div class="p-3 border rounded d-sm-flex">
                    <span class="mb-3 mb-sm-0 bg-warning text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="font-size: 1.2em; min-width: 40px; height: 40px">
                        <i class="fa fa-user"></i>
                    </span>

                    <div class="ml-sm-3 w-100">
                        <h4 class="font-weight-700">{{ $totals['users'].' Users' }}</h4>
                        <p class="mb-3">
                            Guard accounts that can log into the mobile app
                        </p>

                        <a href="{{ route('admin.users') }}" class="btn shadow-none btn-sm btn-block btn-danger">
                            View Users
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4 col-lg-6 col-xl-4 mb-4 mb-xl-0">
                <div class="p-3 border rounded d-sm-flex">
                    <span class="mb-3 mb-sm-0 bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle" style="font-size: 1.2em; min-width: 40px; height: 40px">
                        <i class="fa fa-handshake-o"></i>
                    </span>

                    <div class="ml-sm-3 w-100">
                        <h4 class="font-weight-700">{{ $totals['visitors'].' Visitors' }}</h4>
                        <p class="mb-3">
                            Total visitors ever been checked in across all sites
                        </p>

                        <div>
                            <a href="{{ route('admin.visitors') }}" class="btn shadow-none btn-sm btn-block btn-primary">
                                View Visitors
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="card mb-4">
    <div class="card-body">

        <h4 class="mb-4 font-weight-800">Daily Activity</h4>

        <form class="pb-3 mb-3 d-flex align-items-center">
            @php $r = request(); @endphp
            <input type="date" name="date" class="w-auto form-control bg-white mr-3" placeholder="Date..." value="{{ $r->filled('date') ? $r->get('date'):\Carbon\Carbon::today()->format('Y-m-d') }}">

            <select name="site" class="custom-select mr-3" style="width: auto !important">
                <option value="">All Sites</option>
                @foreach(\App\Models\Site::all() as $site)
                <option value="{{ $site->id }}" @if($r->get('site') == $site->id){{ __('selected') }}@endif>{{ $site->name }}</option>
                @endforeach
            </select>

            <button class="btn btn-default shadow-none">Refresh</button>
        </form>

        <div class="row mb-4">
            <div class="col-lg-4 mb-4">
                <canvas id="summary_chart" height="280"></canvas>
            </div>

            <div class="col-lg-8 mb-4 mb-xl-0">
                <canvas id="chart"></canvas>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
    var amount_chart = new Chart(document.querySelector('#chart'), {
        type: 'line',
        data: {
            labels: [
                @for($i=0; $i<24; $i++)
                @php
                $lbl = ($i<12 ? ($i.' AM'):($i > 12 ? (($i - 12).' PM'):'12 Noon'));
                @endphp
                '{{ $lbl }}',
                @endfor
            ],
            datasets: [
                {
                    label: 'Staff',
                    data: [
                        @for($i=0; $i<24; $i++){{ ($activity_data[$i]['staff'] ?? 0).',' }}@endfor
                    ],
                    borderWidth: 1,
                    borderColor: 'dodgerblue',
                    type: 'line'
                },
                {
                    label: 'Visitors',
                    data: [
                        @for($i=0; $i<24; $i++){{ ($activity_data[$i]['visitors'] ?? 0).',' }}@endfor
                    ],
                    borderWidth: 2,
                    borderColor: 'coral',
                    type: 'line',
                }
            ]
        },
        options: {
            spanGaps: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

var invoices_volume = new Chart(document.querySelector('#summary_chart'), {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($summaries as $s){!! "'".$s['label']."'," !!}@endforeach
        ],
        datasets: [{
            label: 'Invoice Volume',
            data: [
                @foreach($summaries as $s){{ $s['value'].',' }}@endforeach
            ],
            borderWidth: 2,
            backgroundColor: [
                @foreach($summaries as $s){!! "'".$s['color']."'," !!}@endforeach
            ]
        }]
    },
    options: {}
});

</script>
@endsection
