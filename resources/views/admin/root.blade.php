<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Magnate Ventures | Visitor Management | Administration')</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('links')
</head>
<body>

    <?php
$current_route = \Illuminate\Support\Facades\Route::current()->getName();
?>

{{-- @section('body') --}}

<aside class="sidebar">
    <div class="scroll-wrapper">

        <div class="sidebar-header">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}"><strong class="text-white">{{ config('app.name') }}</strong></a>
        </div>

        <div class="scroll-inner">

            <style>
                .navbar-nav{margin-bottom: .5rem}

                .sidebar-heading i{
                    transition: .3s all !important;
                    transform: rotate(-90deg);
                }

                .sidebar-heading.open i{
                    transform: rotate(0deg);
                }
            </style>

            <div class="sidebar-inner py-3">
                <div>

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link @if($current_route == 'admin.dashboard'){{ __('active') }}@endif " href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-line-chart text-primary mr-1"></i>
                                <span class="nav-link-text">Dashboard</span>
                            </a>
                        </li>

                    </ul>

                    <!-- Users -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted @if(preg_match('/admin\.users/', $current_route) || $current_route == 'admin.history.logins'){{ __('open') }}@endif" onclick="$(this).toggleClass('open')" data-toggle="collapse" data-target="#nav-users">
                            Users
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.users/', $current_route) || $current_route == 'admin.history.logins'){{ __('show') }}@endif" id="nav-users">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.users'){{ __('active') }}@endif " href="{{ route('admin.users') }}">
                                    <i class="fa fa-user-circle text-default mr-1"></i>
                                    <span class="nav-link-text">Manage Users</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.users.add'){{ __('active') }}@endif " href="{{ route('admin.users.add') }}">
                                    <i class="fa fa-user-plus text-default mr-1"></i>
                                    <span class="nav-link-text">Add User</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.history.logins'){{ __('active') }}@endif " href="{{ route('admin.history.logins') }}">
                                    <i class="fa fa-user text-info mr-1"></i>
                                    <span class="nav-link-text">Login History</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Sites -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted @if(preg_match('/admin\.sites/', $current_route)){{ __('open') }}@endif" onclick="$(this).toggleClass('open')" data-toggle="collapse" data-target="#nav-sites">
                            Sites
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.sites/', $current_route)){{ __('show') }}@endif" id="nav-sites">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites'){{ __('active') }}@endif " href="{{ route('admin.sites') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">Manage Existing</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites.add'){{ __('active') }}@endif " href="{{ route('admin.sites.add') }}">
                                    <i class="fa fa-plus-square text-default mr-1"></i>
                                    <span class="nav-link-text">Add Site</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Vehicles -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted @if(preg_match('/admin\.vehicles/', $current_route)){{ __('open') }}@endif" onclick="$(this).toggleClass('open')" data-toggle="collapse" data-target="#nav-vehicles">
                            Vehicles
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.vehicles/', $current_route)){{ __('show') }}@endif" id="nav-vehicles">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.vehicles'){{ __('active') }}@endif " href="{{ route('admin.vehicles') }}">
                                    <i class="fa fa-car text-default mr-1"></i>
                                    <span class="nav-link-text">Company Vehicles</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.vehicles.history'){{ __('active') }}@endif " href="{{ route('admin.vehicles.history') }}">
                                    <i class="fa fa-car text-default mr-1"></i>
                                    <span class="nav-link-text">In/Out History</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.vehicles.other'){{ __('active') }}@endif " href="{{ route('admin.vehicles.other') }}">
                                    <i class="fa fa-car text-default mr-1"></i>
                                    <span class="nav-link-text">Other Vehicles</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.vehicles.drivers'){{ __('active') }}@endif " href="{{ route('admin.vehicles.drivers') }}">
                                    <i class="fa fa-user text-default mr-1"></i>
                                    <span class="nav-link-text">Drivers</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Visitors -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted @if(preg_match('/admin\.visitors/', $current_route)){{ __('open') }}@endif" onclick="$(this).toggleClass('open')" data-toggle="collapse" data-target="#nav-visitors">
                            Visitors
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.visitors/', $current_route)){{ __('show') }}@endif" id="nav-visitors">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.visitors'){{ __('active') }}@endif " href="{{ route('admin.visitors') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">View All</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.visitors.visits'){{ __('active') }}@endif " href="{{ route('admin.visitors.visits') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">Visits</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Stats -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted open" onclick="$(this).toggleClass('open')" data-toggle="collapse" data-target="#nav-stats">
                            More
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse show" id="nav-stats">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.staff.checkins'){{ __('active') }}@endif " href="{{ route('admin.staff.checkins') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">Staff Checkins</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.change_password'){{ __('active') }}@endif " href="{{ route('admin.change_password') }}">
                                    <i class="fa fa-lock text-default mr-1"></i>
                                    <span class="nav-link-text">Change Password</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</aside>

<nav class="sticky-nav section-shaped sticky-top bg-primary navbar py-2 d-none align-items-center">
    <div class="col-lg-11 col-xl-11 d-flex align-items-center mx-auto">
        <h3 class="mb-0 d-flex align-items-center font-weight-600 page-heading" style="color: #fff !important">
            @yield('page_icon', '')
            @yield('page_heading', 'Dashboard')
        </h3>

        <div class="dropdown float-right ml-auto">
            <ul class="dropdown-menu">
                <li class="dropdown-item py-0">
                    <a href="{{ route('admin.change_password') }}" class="py-3 d-flex align-items-center text-border">
                        <i class="fa fa-lock mr-3 text-success"></i>Change Password
                    </a>
                </li>

                <li class="dropdown-divider my-0"></li>

                <li class="dropdown-item py-0">
                    <a href="" class="py-3 d-flex align-items-center text-border">
                        <i class="fa fa-power-off mr-3 text-danger"></i>Log Out
                    </a>
                </li>
            </ul>

            <div class="float-right ml-auto d-flex align-items-center dropdown-toggle btn btn-outline-white py-2" data-toggle="dropdown">
                Administrator
            </div>
        </div>
    </div>
</nav>

<main class="main-content position-relative">
    <nav class="header section-shaped position-absolute top-0 left-0 right-0">

        <div class="shape shape-style-1 bg-gradient-primary">
            <span class="shape-50"></span>
            <span class="shape-100"></span>
            <span class="shape-150"></span>
            <span class="shape-100"></span>
            <span class="shape-100"></span>
            <span class="shape-200"></span>
            <span class="shape-50"></span>
        </div>

        <style>
            .page-heading, .page-heading i{ color: #fff !important; }
            .page-heading i{
                display: inline-flex;
                width: 35px;
                height: 35px;
                border-radius: 50%;
                background: #eeeeee50;
                align-items: center;
                justify-content: center;
                font-size: .6em !important;
            }
        </style>
        <div class="nav-inner">
            <div class="my-3 mx-4 d-flex align-items-center">
                <h3 class="mb-0 d-flex align-items-center font-weight-600 page-heading" style="color: #fff !important">
                    @hasSection('page_icon')
                        <span class="mr-3 d-inline-flex">
                            @yield('page_icon', '')
                        </span>
                    @endif
                    @yield('page_heading', 'Dashboard')
                </h3>
            </div>
        </div>

    </nav>

    <div class="dropdown float-right ml-auto position-absolute top-0 right-0 m-3 user-menu" style="z-index: 110">
        <ul class="dropdown-menu">
            <li class="dropdown-item py-0">
                <a href="{{ route('admin.change_password') }}" class="py-3 d-flex align-items-center text-border">
                    <i class="fa fa-lock mr-3 text-success"></i>Change Password
                </a>
            </li>

            <li class="dropdown-divider my-0"></li>

            <li class="dropdown-item py-0">
                <a href="{{ route('admin.logout') }}" class="py-3 d-flex align-items-center text-border">
                    <i class="fa fa-power-off mr-3 text-danger"></i>Log Out
                </a>
            </li>
        </ul>

        <div class="float-right ml-auto d-flex align-items-center dropdown-toggle btn btn-outline btn-link text-white px-2 py-2" data-toggle="dropdown">
            <i class="fa fa-user-circle mr-2"></i>{{ auth('admin')->user()->name }}
        </div>
    </div>

    <div class="py-4 px-3 content">
        <div class="col-lg-12 col-xl-12 mx-auto" style="">
            @yield('content')
        </div>
    </div>
</main>

<div id="custom_alert" class="modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body py-">
                <div class="modal-heade mb-3">
                    <h4 class="modal-title font-weight-600 mb-0" id="alert_title">Alert</h4>
                </div>

                <div id="alert_message"></div>

                <div class="text-right">
                    <button class="btn btn-link px-0 py-1" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/popper/popper.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/bootstrap.min.js') }}"></script>

<script>
    function showAlert(msg, title = null){
        $('#alert_message').text(msg);
        if(title != null) $('#alert_title').text(title);
        $('#custom_alert').modal();
    }
</script>

@yield('scripts')

@if(session()->has('status'))
<script>
    showAlert("{{ session()->get('status') }}", "Info");
</script>
@endif

@if($errors->has('status'))
<script>
    showAlert("{{ $errors->get('status')[0] }}", "Error");
</script>
@endif

</body>
</html>
