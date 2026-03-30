<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">


<head>


    <meta charset="utf-8" />
    <title>Enapel | Login </title>
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

</head>


<!-- Top Bar Start -->

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
                                        <a href="{{ route('dashboard') }}" class="logo logo-admin">
                                            <img src="{{ asset('assets/images/logo_green.png') }}" loading="lazy"
                                                style="width: 240px; height: auto;" alt="logo" class="auth-logo">
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    {{-- Form --}}
                                    <form class="my-4" method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                placeholder="Enter username">
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" for="userpassword">Password</label>
                                            <input type="password" class="form-control" name="password"
                                                id="userpassword" placeholder="Enter password">
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div class="form-group row mt-3">
                                            <div class="col-sm-6">
                                                <div class="form-check form-switch form-switch-success">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="customSwitchSuccess">
                                                    <label class="form-check-label" for="customSwitchSuccess">Remember
                                                        me</label>
                                                </div>
                                            </div><!--end col-->
                                            <div class="col-sm-6 text-end">
                                                @if (Route::has('password.request'))
                                                    <a href="{{ route('password.request') }}"
                                                        class="text-mutedfont-13"><i class="dripicons-lock"></i> Forgot
                                                        password?</a>
                                                @endif
                                            </div><!--end col-->
                                        </div>



                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary" type="submit">Log In <i
                                                            class="fas fa-sign-in-alt ms-1"></i></button>
                                                </div>
                                            </div><!--end col-->
                                        </div>
                                    </form>
                                    <div class="text-center mb-2">
                                        <p class="text-muted">Don't have an account ? <a href="{{ route('register') }}"
                                                class="text-primary ms-2">Register</a></p>
                                    </div>

                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!-- container -->


</body>


</html>
