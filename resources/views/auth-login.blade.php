<!DOCTYPE html>



<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="/assets/" data-template="vertical-menu-template">



<head>

    <meta charset="utf-8" />

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />



    <title>Login</title>



    <meta name="description" content="" />



    <!-- Favicon -->

    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />



    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />



    <!-- Icons -->

    <link rel="stylesheet" href="/assets/vendor/fonts/fontawesome.css" />

    <link rel="stylesheet" href="/assets/vendor/fonts/tabler-icons.css" />

    <link rel="stylesheet" href="/assets/vendor/fonts/flag-icons.css" />



    <!-- Core CSS -->

    <link rel="stylesheet" href="/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />

    <link rel="stylesheet" href="/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />

    <link rel="stylesheet" href="/assets/css/demo.css" />

    <link rel="stylesheet" href="/assets/css/login.css" />





    <!-- Vendors CSS -->

    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="/assets/vendor/libs/node-waves/node-waves.css" />

    <link rel="stylesheet" href="/assets/vendor/libs/typeahead-js/typeahead.css" />

    <!-- Vendor -->

    <link rel="stylesheet" href="/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />



    <!-- Page CSS -->

    <!-- Page -->

    <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->

    <script src="/assets/vendor/js/helpers.js"></script>



    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->

    <script src="/assets/vendor/js/template-customizer.js"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="/assets/js/config.js"></script>

    <style>



    </style>

</head>



<body>

    <!-- Content -->



    <div class="authentication-wrapper authentication-cover authentication-bg">

        <div class="authentication-inner row">




            <div class="col-lg-3"></div>

            <div class="d-flex col-12 col-lg-6 align-items-center p-sm-5 p-4">

                <div class="w-px-400 mx-auto p-4">

                    <div class="image mb-3 mt-3">

                        <img src="/assets/img/App logo.png" alt="">

                    </div>




                    <form id="" class="mb-3" method="post" action="{{ Request::url() }}">

                        @csrf

                        @if ($errors->has('email') || $errors->has('password'))

                            <div class="alert alert-danger">

                                @if ($errors->has('email'))
                                    {{ $errors->first('email') }}<br>
                                @endif
                                {{ $errors->first('password') }}



                            </div>

                        @endif

                        <div class="mb-3">

                            <label for="email" class="form-label">Email Address</label>

                            <input type="text" class="form-control" id="email" value="{{ old('email') }}"
                                name="email" placeholder="Enter your email" />
                        </div>

                        <div class="mb-5 form-password-toggle">

                            <label for="email" class="form-label">Password</label>



                            <div class="input-group input-group-merge">



                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />

                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>



                            </div>



                        </div>



                       
                        
                        
                        <button class="btn btn-primary d-grid w-100" id="signinButton" onclick="showLoader()">
                            <span id="btntext"> Sign in</span>
                            <span class="align-middle mt-1" id="loader" role="status" style="display: none;">
                                <span class="spinner-border text-dark" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                            </span>
                        </button>

                    </form>





                </div>

            </div>

            <div class="col-lg-3"></div>




            <!-- /Login -->

        </div>



    </div>



    <!-- / Content -->



    <!-- Core JS -->
    <script>
        function showLoader() {
            var btntext = document.getElementById('btntext');
            var loader = document.getElementById('loader');

            btntext.style.display = 'none';
            loader.style.display = 'block';
        }
    </script>
    <!-- build:js assets/vendor/js/core.js -->

    <script src="/assets/vendor/libs/jquery/jquery.js"></script>

    <script src="/assets/vendor/libs/popper/popper.js"></script>

    <script src="/assets/vendor/js/bootstrap.js"></script>

    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="/assets/vendor/libs/node-waves/node-waves.js"></script>



    <script src="/assets/vendor/libs/hammer/hammer.js"></script>

    <script src="/assets/vendor/libs/i18n/i18n.js"></script>

    <script src="/assets/vendor/libs/typeahead-js/typeahead.js"></script>



    <script src="/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->



    <!-- Vendors JS -->

    <script src="/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>

    <script src="/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>

    <script src="/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>



    <!-- Main JS -->

    <script src="/assets/js/main.js"></script>



    <!-- Page JS -->

    <script src="/assets/js/pages-auth.js"></script>


</body>



</html>
