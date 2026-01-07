@php
    $containerNav = $containerNav ?? 'container-xxl';
    $navbarDetached = $navbarDetached ?? '';
@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme"
        id="layout-navbar">
@endif
    @if(isset($navbarDetached) && $navbarDetached == '')
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="{{$containerNav}}">
    @endif

            <!--  Brand demo (display only for navbar-full and hide on below xl) -->
            @if(isset($navbarFull))
                <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
                    <a href="{{url('/')}}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            @include('_partials.macros', ["height" => 20])
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">{{config('variables.templateName')}}</span>
                    </a>
                </div>
            @endif

            <!-- ! Not required for layout-without-menu -->
            @if(!isset($navbarHideToggle))
                <div
                    class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="ti ti-menu-2 ti-sm"></i>
                    </a>
                </div>
            @endif

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

                <!-- Search -->
                <div class="navbar-nav align-items-center">
                    <div class="nav-item navbar-search-wrapper mb-0">
                        <a class="nav-item nav-link search-toggler d-flex align-items-center px-0"
                            href="javascript:void(0);">
                            <i class="ti ti-search ti-md me-2"></i>
                            <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                        </a>
                    </div>
                </div>
                <!-- /Search -->

                <ul class="navbar-nav flex-row align-items-center ms-auto">
                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                            data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                                    alt class="h-auto rounded-circle">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/1.png') }}"
                                                    alt class="h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-semibold d-block">
                                                @if (Auth::check())
                                                    {{ Auth::user()->name }}
                                                @else
                                                    John Doe
                                                @endif
                                            </span>
                                            <small class="text-muted">Admin</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                {{-- @if (Auth::check())
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class='ti ti-logout me-2'></i>
                                            <span class="align-middle">Logout</span>
                                        </a>
                                    </form>
                                @else
                                    <a class="dropdown-item" href="{{ route('login') }}">
                                        <i class='ti ti-login me-2'></i>
                                        <span class="align-middle">Login</span>
                                    </a>
                                @endif --}}
                            </li>
                        </ul>
                    </li>
                    <!--/ User -->
                </ul>
            </div>

            <!-- Search Small Screens -->
            <div
                class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
                <input type="text"
                    class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0"
                    placeholder="Search..." aria-label="Search...">
                <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
            </div>
            @if(isset($navbarDetached) && $navbarDetached == '')
                </div>
            @endif
    </nav>
    <!-- / Navbar -->