<!DOCTYPE html>
<!--
Author: Rendy Irawan
Product Name: DineSync POS
Website: http://www.dinesync.id
Contact: support@dinesync.id
License: Proprietary - DineSync POS System
-->
<html lang="en">
<!--begin::Head-->

<head>

    <title>@yield('title') - LaundrySync POS</title>
    <meta charset="utf-8" />
    <meta name="description" content="LaundrySync POS - Solusi Manajemen Laundry Modern, Kasir Pintar, Kiloan & Satuan, dan Sistem Antrean Terintegrasi." />
    <meta name="keywords" content="pos, point of sale, kasir laundry, laundry kiloan, laundry satuan, logistik kurir, laundrysync, aplikasi laundry" />
    <meta name="author" content="Rendy Irawan" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="LaundrySync POS - Powering Your Laundry Business" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="LaundrySync POS" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/laundry-sync-logo.png') }}" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>

    @stack('stylesheets')
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Page bg image-->
        <style>
            body {
                background-image: url('{{ asset('assets/media/patterns/circuit-board.svg') }}');
            }

            [data-bs-theme="dark"] body {
                background-image: url('{{ asset('assets/media/auth/bg10-dark.jpeg') }}');
            }
        </style>
        <!--end::Page bg image-->
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            @yield('content')
            <!--end::Body-->
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                    <!--begin::Image-->
                    <img class="theme-light-show mx-auto mw-100 w-150px w-lg-250px mb-10 mb-lg-15"
                        src="{{ asset('assets/media/logos/laundry-sync-logo.png') }}" alt="Logo" />

                    <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-250px mb-10 mb-lg-15"
                        src="{{ asset('assets/media/logos/laundry-sync-logo.png') }}" alt="Logo" />

                    <!--end::Image-->
                    <!--begin::Title-->
                    <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">
                        LaundrySync <br> Laundry POS System
                    </h1>
                    <!--end::Title-->
                    <!--begin::Text-->
                    <div class="text-gray-600 fs-base text-center fw-semibold max-w-400px">
                        Aplikasi Point of Sale cerdas untuk membantu bisnis laundry Anda dalam mengelola transaksi kiloan & satuan, status produksi workshop, loyalitas member, dan pelacakan kurir logistik.
                        <br /><br />
                        <span class="badge badge-light-primary fs-7 fw-bold">PT. LaundrySync Indonesia</span>
                    </div>
                    <!--end::Text-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->

    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    @stack('scripts')
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>
