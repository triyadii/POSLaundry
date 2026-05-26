<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="275px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_toggle">
    <div class="d-flex flex-stack px-4 px-lg-6 py-3 py-lg-8" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
            <img alt="Logo" src="{{ asset('assets/media/logos/laundry-sync-logo.png') }}"
                class="h-35px h-lg-45px theme-light-show" />
            <img alt="Logo" src="{{ asset('assets/media/logos/laundry-sync-logo.png') }}"
                class="h-35px h-lg-45px theme-dark-show" />
            <span class="ms-3 fs-3 fw-bolder text-gray-900 theme-light-show" style="letter-spacing: 0.5px;">LaundrySync</span>
            <span class="ms-3 fs-3 fw-bolder text-white theme-dark-show" style="letter-spacing: 0.5px;">LaundrySync</span>
        </a>
        <div class="ms-3">
            <div class="cursor-pointer position-relative symbol symbol-circle symbol-40px"
                data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                data-kt-menu-placement="bottom-end">
                <img src="{{ asset('storage/user/avatar/' . Auth::user()->avatar) }}" alt="user" />
                <div class="position-absolute rounded-circle bg-success start-100 top-100 h-8px w-8px ms-n3 mt-n3">
                </div>
            </div>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                data-kt-menu="true">
                <div class="menu-item px-3">
                    <div class="menu-content d-flex align-items-center px-3">
                        <div class="symbol symbol-50px me-5">
                            <img alt="Logo" src="{{ asset('storage/user/avatar/' . Auth::user()->avatar) }}" />
                        </div>
                        <div class="d-flex flex-column">
                            <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::user()->name ?? 'User' }}
                                <span
                                    class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{ Auth::user()->roles->pluck('name')->first() ?? 'Staff' }}</span>
                            </div>
                            <a href="#"
                                class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->email ?? '' }}</a>
                        </div>
                    </div>
                </div>
                <div class="separator my-2"></div>
                <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                    data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                    <a href="#" class="menu-link px-5">
                        <span class="menu-title position-relative">Mode
                            <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                                <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                            </span></span>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                        data-kt-menu="true" data-kt-element="theme-mode-menu">
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-night-day fs-2"></i>
                                </span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-moon fs-2"></i>
                                </span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                        <div class="menu-item px-3 my-0">
                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-outline ki-screen fs-2"></i>
                                </span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="menu-item px-5 my-1">
                    <a href="{{ route('account.index') }}" class="menu-link px-5">My Profile</a>
                </div>
                <div class="menu-item px-5">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" id="logout-btn" class="menu-link px-5">
                            <span class="menu-icon"><i class="ki-outline ki-exit-right fs-2 text-danger"></i></span>
                            Sign Out
                        </a>
                    </form>
                </div>

                <script>
                    document.getElementById('logout-btn').addEventListener('click', function(e) {
                        e.preventDefault();

                        Swal.fire({
                            icon: 'question',
                            title: '<span class="fw-bold">Keluar dari Akun?</span>',
                            html: '<span class="text-muted fs-6">Sesi Anda akan diakhiri. Sampai jumpa lagi! 👋</span>',
                            showCancelButton: true,
                            confirmButtonText: '<i class="ki-outline ki-exit-right fs-5 me-1"></i> Ya, Sign Out',
                            cancelButtonText: 'Batal',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-danger me-3',
                                cancelButton: 'btn btn-light'
                            },
                            reverseButtons: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Inject dot-loader style once
                                if (!document.getElementById('dot-loader-style-logout')) {
                                    const s = document.createElement('style');
                                    s.id = 'dot-loader-style-logout';
                                    s.textContent = `
                                        .dot-out { width: 12px; height: 12px; background-color: #f1416c; border-radius: 50%; animation: bounceOut 0.6s infinite alternate; }
                                        .dot-out--2 { animation-delay: 0.15s; }
                                        .dot-out--3 { animation-delay: 0.3s; }
                                        @keyframes bounceOut { 0% { transform: translateY(0); opacity: 1; } 100% { transform: translateY(-10px); opacity: 0.4; } }
                                    `;
                                    document.head.appendChild(s);
                                }

                                let timerInterval;
                                Swal.fire({
                                    icon: 'warning',
                                    title: '<span class="fw-bold">Sedang Keluar...</span>',
                                    html: `
                                        <div class="text-muted mb-3">Mengakhiri sesi Anda...</div>
                                        <div class="my-6" style="display:flex;justify-content:center;gap:10px;">
                                            <div class="dot-out"></div>
                                            <div class="dot-out dot-out--2"></div>
                                            <div class="dot-out dot-out--3"></div>
                                        </div>
                                        <div class="progress bg-secondary mt-3" style="height:12px;border-radius:20px;overflow:hidden;">
                                            <div id="logout-progress-bar" class="progress-bar bg-danger" style="width:0%;border-radius:20px;"></div>
                                        </div>
                                        <div id="logout-percent" class="mt-2 fw-bold text-gray-700">0%</div>
                                    `,
                                    width: 400,
                                    padding: '2em',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    timer: 1800,
                                    didOpen: () => {
                                        let bar = document.getElementById('logout-progress-bar');
                                        let pct = document.getElementById('logout-percent');
                                        let width = 0;
                                        timerInterval = setInterval(() => {
                                            width += Math.floor(Math.random() * 6) + 2;
                                            if (width > 100) width = 100;
                                            if (bar) bar.style.width = width + '%';
                                            if (pct) pct.innerHTML = width + '%';
                                            if (width >= 100) clearInterval(timerInterval);
                                        }, 50);
                                    },
                                    willClose: () => { clearInterval(timerInterval); }
                                }).then(() => {
                                    document.getElementById('logout-form').submit();
                                });
                            }
                        });
                    });
                </script>

            </div>
        </div>
    </div>
    <div class="flex-column-fluid px-4 px-lg-8 py-4" id="kt_app_sidebar_nav">
        <div id="kt_app_sidebar_nav_wrapper" class="d-flex flex-column hover-scroll-y pe-4 me-n4" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar, #kt_app_sidebar_nav" data-kt-scroll-offset="5px">

            <div class="mb-6 mt-4">
                <h3 class="text-gray-800 fw-bold mb-8">Menu Utama</h3>
                <div class="row row-cols-3 g-2" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">

                    {{-- HOME --}}
                    <div class="col mb-2">
                        <a href="{{ route('dashboard') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('dashboard') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-home fs-2hx mb-2 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Home</span>
                        </a>
                    </div>

                    {{-- KASIR: Order --}}
                    @can('view_kasir')
                    <div class="col mb-2">
                        <a href="{{ route('order.index') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('order.*') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-shop fs-2hx mb-2 {{ request()->routeIs('order.*') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Order</span>
                        </a>
                    </div>
                    @endcan

                    {{-- MASTER: Pelanggan --}}
                    @can('view_data_master')
                    <div class="col mb-2">
                        <a href="{{ route('customers.index') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('customers.*') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-people fs-2hx mb-2 {{ request()->routeIs('customers.*') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Customer</span>
                        </a>
                    </div>
                    @endcan

                    {{-- MASTER: Layanan --}}
                    @can('view_data_master')
                    <div class="col mb-2">
                        <a href="{{ route('services.index') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('services.*') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-price-tag fs-2hx mb-2 {{ request()->routeIs('services.*') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Layanan</span>
                        </a>
                    </div>
                    @endcan

                    {{-- MASTER: Promo --}}
                    @can('view_data_master')
                    <div class="col mb-2">
                        <a href="{{ route('promos.index') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('promos.*') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-gift fs-2hx mb-2 {{ request()->routeIs('promos.*') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Promo</span>
                        </a>
                    </div>
                    @endcan

                    {{-- STAFF: Users --}}
                    @can('view_resources')
                    <div class="col mb-2">
                        <a href="{{ route('users.index') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('users.*') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-profile-user fs-2hx mb-2 {{ request()->routeIs('users.*') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Staff</span>
                        </a>
                    </div>
                    @endcan

                    {{-- HAK AKSES: Roles --}}
                    @can('view_resources')
                    <div class="col mb-2">
                        <a href="{{ route('roles.index') }}"
                            class="btn btn-icon btn-outline btn-bg-light btn-active-light-primary btn-flex flex-column flex-center w-lg-90px h-lg-90px w-70px h-70px border-gray-200 {{ request()->routeIs('roles.*') ? 'active btn-light-primary border-primary' : '' }}"
                            data-kt-button="true">
                            <i class="ki-outline ki-shield fs-2hx mb-2 {{ request()->routeIs('roles.*') ? 'text-primary' : 'text-gray-700' }}"></i>
                            <span class="fs-7 fw-bold">Role</span>
                        </a>
                    </div>
                    @endcan

                </div>
            </div>
        </div>
    </div>
    <div class="flex-column-auto d-flex flex-center px-4 px-lg-8 py-3 py-lg-8" id="kt_app_sidebar_footer">
        <div class="app-footer-item">
            <a href="{{ route('settings.index') }}"
                class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px">
                <i class="ki-outline ki-setting-2 fs-2"></i>
            </a>
        </div>
    </div>
</div>
