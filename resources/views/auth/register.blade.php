<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Enapel | Activate Software</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="container-xxl">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card">
                                <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                    <div class="text-center p-0">
                                        <a href="index.html" class="logo logo-admin">
                                            <img src="{{ asset('assets/images/logo_green.png') }}" loading="lazy"
                                                style="width: 240px; height: auto;" alt="logo" class="auth-logo">
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    {{-- Form --}}
                                    <form class="my-4" method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <!-- Hidden Inputs -->
                                        <input type="hidden" class="form-control" name="name" id="name">
                                        <input type="hidden" class="form-control" name="business_name"
                                            id="business_name">
                                        <input type="hidden" class="form-control" name="logo" id="logo">
                                        <input type="hidden" class="form-control" name="module" id="module">
                                        <input type="hidden" name="email" id="hidden_email">

                                        <!-- License Key Field -->
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="license_key">License Key</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="license_key"
                                                    name="license_key" placeholder="Enter License Key">
                                                <button type="button" class="btn btn-success"
                                                    id="validateLicense">Validate</button>
                                            </div>
                                            <div id="licenseStatus" class="mt-2"></div>
                                        </div>

                                        <!-- Email Field (Disabled Initially) -->
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                placeholder="Enter email" disabled>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <!-- Password Field (Disabled Initially) -->
                                        <div class="form-group">
                                            <label class="form-label" for="userpassword">Password</label>
                                            <input type="password" class="form-control" name="password"
                                                id="userpassword" placeholder="Enter password" disabled>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>




                                        <div class="form-group">
                                            <label class="form-label" for="password_confirmation">Confirm
                                                Password</label>
                                            <input type="password" class="form-control" name="password_confirmation"
                                                id="password_confirmation" placeholder="Confirm password" disabled>
                                            <x-input-error :messages="$errors->get('password_confirmation')" />
                                        </div>



                                        <!-- Login Button (Initially Disabled) -->
                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary" type="submit" id="loginButton"
                                                        disabled>Create Super User
                                                        <i class="fas fa-sign-in-alt ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center mb-2">
                                            <p class="text-muted">Already activated account? <a
                                                    href="{{ route('login') }}" class="text-primary ms-2">Login</a>
                                            </p>
                                        </div>
                                    </form>

                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!-- container -->

    <script>
        $(document).ready(function() {
            $('#validateLicense').click(function() {
                let licenseKey = $('#license_key').val().trim();

                if (licenseKey === '') {
                    $('#licenseStatus').html(
                        '<span class="text-danger">License key cannot be empty.</span>');
                    return;
                }

                $('#validateLicense').prop('disabled', true).text('Validating...');

                $.ajax({
                    url: 'http://192.168.0.200:8000/api/v1/license_key',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        license_key: licenseKey
                    }),
                    success: function(response) {
                        if (response.message === 'License key is active') {
                            $('#licenseStatus').html('<span class="text-success">✅ ' + response
                                .message + '</span>');

                            $('#userpassword, #password_confirmation, #loginButton')
                                .prop('disabled', false);

                            // Set the value of the disabled email field and keep it visible
                            $('#email').val(response.data.email); // Populate email field
                            // Set the value of the hidden email field for submission
                            $('#hidden_email').val(response.data
                                .email); // Populate hidden email field

                            $('#module').val(response.data.module);
                            $('#name').val(response.data.name);
                            $('#business_name').val(response.data.company_name);
                            $('#logo').val(response.data.logo);
                        } else {
                            $('#licenseStatus').html('<span class="text-warning">⚠️ ' + response
                                .message + '</span>');

                            $('#userpassword, #password_confirmation, #loginButton')
                                .prop('disabled', true);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '❌ Error validating license key.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = '❌ ' + xhr.responseJSON.message;
                        }
                        $('#licenseStatus').html('<span class="text-danger">' + errorMessage +
                            '</span>');

                        $('#userpassword, #password_confirmation, #loginButton').prop(
                            'disabled', true);
                    },
                    complete: function() {
                        $('#validateLicense').prop('disabled', false).text('Validate');
                    }
                });
            });
        });
    </script>


</body>

</html>
