<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<!-- Mirrored from www.pixelwibes.com/template/ebazar/html/dist/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 08 Mar 2022 07:02:05 GMT -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dry Clean </title>
    <link rel="icon" href="{{asset('assets/admin/favicon.ico')}}" type="image/x-icon"> <!-- Favicon-->

    @include('admin.inc.style')
    <style>
        .tawk-min-container {
    display: none !important;
}
    </style>
    @yield('style')
</head>

<body>
    <div id="ebazar-layout" class="theme-blue">

        <!-- sidebar -->
        @include('admin.inc.sidebar')

        <!-- main body area -->
        <div class="main px-lg-4 px-md-4">

            <!-- Body: Header -->
            @include('admin.inc.header')

            @stack('content')
            @yield('content')



        </div>

    </div>

    @include('admin.inc.script')
    @yield('script')
</body>

<!-- Mirrored from www.pixelwibes.com/template/ebazar/html/dist/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 08 Mar 2022 07:02:20 GMT -->

</html>