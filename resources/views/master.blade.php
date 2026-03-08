<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Enapel" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/logo_icon.png')}}">
    <link rel="stylesheet" href="{{asset('assets/libs/jsvectormap/css/jsvectormap.min.css')}}">

    <!-- App css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/simple-datatables/style.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('head_script')
</head>

<body>
    @include('partials.topbar')
    @include('partials.sidebar')

    <div class="page-wrapper">
        <div class="page-content">
            @yield('content')
            @include('partials.footer')
        </div>
    </div>

    <script src="{{asset("assets/libs/bootstrap/js/bootstrap.bundle.min.js")}}"></script>
    <script src="{{asset("assets/libs/simplebar/simplebar.min.js")}}"></script>

    <script src="{{asset("assets/libs/apexcharts/apexcharts.min.js")}}"></script>
    <script src="{{asset("assets/data/stock-prices.js")}}"></script>
    <script src="{{asset("assets/libs/jsvectormap/js/jsvectormap.min.js")}}"></script>
    <script src="{{asset("assets/libs/jsvectormap/maps/world.js")}}"></script>
    <script src="{{asset("assets/js/pages/index.init.js")}}"></script>
    <script src="{{asset("assets/js/app.js")}}"></script>
    <script src="{{asset('assets/js/pages/ecommerce-index.init.js')}}"></script>
    @yield('body_script')
    <!-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("startbarCollapse");
            const toggleButton = document.getElementById("togglemenu");

            // Ensure sidebar is open by default
            sidebar.classList.add("show");

            // Toggle sidebar open/close
            toggleButton.addEventListener("click", function() {
                sidebar.classList.toggle("show");
            });
        });
    </script> -->
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif

    <!-- SweetAlert for Validation Errors -->
    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
            showConfirmButton: true
        });
    </script>
    @endif
</body>

</html>