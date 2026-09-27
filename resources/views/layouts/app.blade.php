<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Kopi Pusat')
    </title>


    {{-- =====================================================
         BOOTSTRAP
    ====================================================== --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    {{-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    {{-- =====================================================
         APPLICATION ASSETS
    ====================================================== --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

</head>


<body class="app-body">

    <div class="app-wrapper">


        {{-- =================================================
             SIDEBAR
        ================================================== --}}

        @include('components.admin.sidebar')


        {{-- =================================================
             MAIN
        ================================================== --}}

        <main class="app-main">


            {{-- =============================================
                 TOPBAR
            ============================================== --}}

            <header class="app-topbar">


                {{-- MOBILE MENU --}}

                <button type="button" class="mobile-menu-button" id="mobileMenuButton" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>


                <div class="topbar-spacer"></div>


                {{-- USER --}}

                <div class="topbar-user">

                    <a href="{{ route('profile.index') }}" class="topbar-user text-decoration-none" title="Profile">

                        <div class="topbar-user-info">

                            <strong>
                                {{ auth()->user()->name }}
                            </strong>

                            <span>
                                {{ ucfirst(auth()->user()->role) }}
                            </span>

                        </div>


                        <div class="topbar-avatar">

                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                        </div>

                    </a>

                </div>

            </header>


            {{-- =============================================
                 PAGE CONTENT
            ============================================== --}}

            <section class="app-content">

                @yield('content')

            </section>

        </main>

    </div>


    {{-- =====================================================
         MOBILE SIDEBAR OVERLAY
    ====================================================== --}}

    <div class="sidebar-overlay" id="sidebarOverlay"></div>


    {{-- =====================================================
         GLOBAL CONFIRMATION MODAL
    ====================================================== --}}

    <div id="confirmationModal" class="confirmation-modal" aria-hidden="true">

        <div class="confirmation-modal-backdrop" data-confirmation-close></div>


        <div class="confirmation-modal-dialog" role="dialog" aria-modal="true"
            aria-labelledby="confirmationModalTitle">

            <div class="confirmation-modal-icon" id="confirmationModalIcon">
                <i class="bi bi-question-lg"></i>
            </div>


            <div class="confirmation-modal-content">

                <h5 id="confirmationModalTitle" class="confirmation-modal-title">
                    Konfirmasi
                </h5>


                <p id="confirmationModalMessage" class="confirmation-modal-message">
                    Apakah Anda yakin?
                </p>

            </div>


            <div class="confirmation-modal-actions">

                <button type="button" class="btn btn-light" data-confirmation-close>
                    Batal
                </button>


                <form id="confirmationModalForm" method="POST" class="m-0">

                    @csrf

                    <input type="hidden" name="_method" id="confirmationModalMethod">


                    <button type="submit" id="confirmationModalSubmit" class="btn btn-primary">
                        Ya, Lanjutkan
                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- =====================================================
         BOOTSTRAP JS
    ====================================================== --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    {{-- =====================================================
         SIDEBAR SCRIPT
    ====================================================== --}}

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const mobileMenuButton =
                    document.getElementById(
                        'mobileMenuButton'
                    );

                const sidebar =
                    document.querySelector(
                        '.app-sidebar'
                    );

                const sidebarOverlay =
                    document.getElementById(
                        'sidebarOverlay'
                    );


                /*
                |--------------------------------------------------------------------------
                | Toggle Sidebar
                |--------------------------------------------------------------------------
                */

                function toggleSidebar() {

                    if (!sidebar || !sidebarOverlay) {
                        return;
                    }

                    sidebar.classList.toggle('show');

                    sidebarOverlay.classList.toggle('show');

                    document.body.classList.toggle(
                        'sidebar-open'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Mobile Menu
                |--------------------------------------------------------------------------
                */

                if (mobileMenuButton) {

                    mobileMenuButton.addEventListener(
                        'click',
                        toggleSidebar
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Overlay
                |--------------------------------------------------------------------------
                */

                if (sidebarOverlay) {

                    sidebarOverlay.addEventListener(
                        'click',
                        toggleSidebar
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Close after navigation
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('.sidebar-link')
                    .forEach(function(link) {

                        link.addEventListener(
                            'click',
                            function() {

                                if (
                                    window.innerWidth < 992
                                ) {

                                    sidebar.classList.remove(
                                        'show'
                                    );

                                    sidebarOverlay.classList.remove(
                                        'show'
                                    );

                                    document.body.classList.remove(
                                        'sidebar-open'
                                    );

                                }

                            }
                        );

                    });


                /*
                |--------------------------------------------------------------------------
                | Close sidebar when resizing to desktop
                |--------------------------------------------------------------------------
                */

                window.addEventListener(
                    'resize',
                    function() {

                        if (
                            window.innerWidth >= 992
                        ) {

                            sidebar?.classList.remove(
                                'show'
                            );

                            sidebarOverlay?.classList.remove(
                                'show'
                            );

                            document.body.classList.remove(
                                'sidebar-open'
                            );

                        }

                    }
                );

            }
        );
    </script>


    @stack('scripts')

</body>

</html>
