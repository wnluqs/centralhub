<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Internal Site Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap Icons & FontAwesome --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    {{-- Your Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Page-specific styles --}}
    @stack('styles')

    <style>
        /* Full-screen background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: url('/images/green.jpg') no-repeat center center fixed;
            z-index: -1;
        }

        /* Make DataTables inputs visible on dark */
        .dataTables_filter input,
        .dataTables_length select {
            background: #fff !important;
            color: #000 !important;
            border: 1px solid #ccc !important;
        }
    </style>
</head>

<body>
    <div class="wrapper d-flex align-items-stretch">
        {{-- Sidebar --}}
        <nav id="sidebar">
            <div class="custom-menu">
                <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
            </div>
            <div class="img bg-wrap text-center py-4" style="background-image: url('{{ asset('images/bg_1.jpg') }}')">
                <div class="user-logo">
                    <div class="img" style="background-image: url('{{ asset('images/profile1.jpg') }}')"></div>
                    <h3>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h3>
                </div>
            </div>
            <ul class="list-unstyled components mb-5">
                <li class="active">
                    <a href="{{ route('dashboard') }}">
                        <span class="fa fa-home mr-3"></span> Home
                    </a>
                </li>
                <li>
                    <a href="#departmentsSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <span class="fa fa-download mr-3"></span> Departments
                    </a>
                    <ul class="collapse list-unstyled" id="departmentsSubmenu">
                        @foreach (['accounting', 'hr', 'operations', 'technical', 'controlCenter', 'secretary'] as $dept)
                            <li>
                                <a href="{{ route("departments.$dept") }}">
                                    {{ ucfirst($dept) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li><a href="#"><span class="fa fa-gift mr-3"></span> Gift Code</a></li>
                <li><a href="#"><span class="fa fa-trophy mr-3"></span> Top Review</a></li>
                <li>
                    <a href="{{ route('admin.settings') }}">
                        <span class="fa fa-cog mr-3"></span> Settings (Admin)
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.form') }}">
                        <span class="fa fa-support mr-3"></span> Support
                    </a>
                </li>
                <li>
                    <a href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="fa fa-sign-out mr-3"></span> Sign Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf
                    </form>
                </li>
            </ul>
        </nav>

        {{-- Main Content --}}
        <div id="content" class="p-4 p-md-5 pt-5">
            @yield('content')
        </div>
    </div>

    <!-- jQuery FIRST -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Core and page scripts -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @stack('scripts')

</body>

</html>
