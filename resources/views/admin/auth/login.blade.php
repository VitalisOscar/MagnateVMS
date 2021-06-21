<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login | VMS | Magnate Ventures</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body class="d-flex align-items-center justify-content-center h-100vh" style="background: #f2f5f8">

    <div class="card shadow-sm rounded" style="width: 100%; max-width: 350px">
        <form action="{{ route('admin.login') }}" method="post">

            @csrf
            
            <div class="card-body">

                <div class="py-4 mb-3 text-center">
                    <img src="{{ asset('img/magnate_logo.png') }}" alt="Logo" class="d-block mb-3 mx-auto" style="height: 70px">
                    <h5 class="font-weight-600">Site Activity Admin</h5>
                    <span class="d-inline-block px-4 rounded bg-primary" style="height: 3px"></span>
                </div>

                <div class="form-group">
                    <strong>Username:</strong>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control mt-1" required>
                    @if ($errors->has('username'))
                    <small class="text-danger">{{ $errors->first('username') }}</small>
                    @else
                    <small>Enter your administrator issued username</small>
                    @endif
                </div>

                <div class="form-group mb-4">
                    <strong>Password:</strong>
                    <input type="password" name="password" value="{{ old('password') }}" class="form-control mt-1" required>
                    @if ($errors->has('password'))
                    <small class="text-danger">{{ $errors->first('password') }}</small>
                    @endif
                </div>

                <div>
                    <button class="btn btn-block btn-primary">Log In</button>
                </div>


            </div>

        </form>
    </div>


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
