<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Magnate Ventures | Visitor Management | Administration')</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

    <?php
$current_route = \Illuminate\Support\Facades\Route::current()->getName();
?>

{{-- @section('body') --}}

<aside class="sidebar">
    <div class="scroll-wrapper">

        <div class="sidebar-header">
            <a class="navbar-brand" href=""><strong class="text-white">{{ config('app.name') }}</strong></a>
        </div>

        <div class="scroll-inner">

            <div class="sidebar-inner py-3">
                <div>

                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link @if($current_route == 'admin.dashboard'){{ __('active') }}@endif " href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-line-chart text-primary mr-1"></i>
                                <span class="nav-link-text">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link @if($current_route == 'admin.adverts'){{ __('active') }}@endif" href="">
                                <i class="fa fa-bullhorn text-yellow mr-1"></i>
                                <span class="nav-link-text">My Account</span>
                            </a>
                        </li>

                    </ul>

                    <!-- Users -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted" data-toggle="collapse" data-target="#nav-users">
                            Users
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.users/', $current_route)){{ __('show') }}@endif" id="nav-users">
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
                                <a class="nav-link @if($current_route == 'admin.users.logins'){{ __('active') }}@endif " href="{{ route('admin.users.logins') }}">
                                    <i class="fa fa-tv text-info mr-1"></i>
                                    <span class="nav-link-text">Login History</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Sites -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted" data-toggle="collapse" data-target="#nav-sites">
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

                    <!-- Company Vehicles -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted" data-toggle="collapse" data-target="#nav-vehicles">
                            Company Vehicles
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.vehicles/', $current_route)){{ __('show') }}@endif" id="nav-vehicles">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.vehicles'){{ __('active') }}@endif " href="{{ route('admin.vehicles') }}">
                                    <i class="fa fa-car text-default mr-1"></i>
                                    <span class="nav-link-text">Vehicles</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.users.add'){{ __('active') }}@endif " href="{{ route('admin.users.add') }}">
                                    <i class="fa fa-user text-default mr-1"></i>
                                    <span class="nav-link-text">Drivers</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Visitors -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted" data-toggle="collapse" data-target="#nav-visitors">
                            Visitors
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.visitors/', $current_route)){{ __('show') }}@endif" id="nav-visitors">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites'){{ __('active') }}@endif " href="{{ route('admin.sites') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">View All</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites'){{ __('active') }}@endif " href="{{ route('admin.sites') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">Visitations</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites.add'){{ __('active') }}@endif " href="{{ route('admin.sites.add') }}">
                                    <i class="fa fa-plus-square text-default mr-1"></i>
                                    <span class="nav-link-text">Visit Stats</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Stats -->
                    <div class="nav-group">
                        <h4 class="sidebar-heading text-muted" data-toggle="collapse" data-target="#nav-visitors">
                            Stats
                            <i class="fa fa-chevron-down float-right toggle-icon"></i>
                        </h4>

                        <ul class="navbar-nav collapse @if(preg_match('/admin\.visitors/', $current_route)){{ __('show') }}@endif" id="nav-visitors">
                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites'){{ __('active') }}@endif " href="{{ route('admin.sites') }}">
                                    <i class="fa fa-map-marker text-default mr-1"></i>
                                    <span class="nav-link-text">Staff</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link @if($current_route == 'admin.sites.add'){{ __('active') }}@endif " href="{{ route('admin.sites.add') }}">
                                    <i class="fa fa-plus-square text-default mr-1"></i>
                                    <span class="nav-link-text">Statistics</span>
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
    <div class="col-lg-11 col-xl-10 d-flex align-items-center mx-auto">
        <h3 class="mb-0 d-flex align-items-center font-weight-600 page-heading" style="color: #fff !important">
            @yield('page_icon', '')
            @yield('page_heading', 'Dashboard')
        </h3>

        <div class="dropdown float-right ml-auto">
            <ul class="dropdown-menu">
                <li class="dropdown-item py-0">
                    <a href="" class="py-3 d-flex align-items-center text-border">
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
            <div class="col-lg-11 col-xl-10 d-flex align-items-center mx-auto">
                <h3 class="mb-0 d-flex align-items-center font-weight-600 page-heading" style="color: #fff !important">
                    @yield('page_icon', '')
                    @yield('page_heading', 'Dashboard')
                </h3>

                <div class="dropdown float-right ml-auto">
                    <ul class="dropdown-menu">
                        <li class="dropdown-item py-0">
                            <a href="" class="py-3 d-flex align-items-center text-border">
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
        </div>

    </nav>

    <div class="py-4 px-3 content">
        <div class="col-lg-11 col-xl-10 mx-auto" style="">
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
