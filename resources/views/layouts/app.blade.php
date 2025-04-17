<!doctype html>
<html lang="en">

<video autoplay loop muted playsinline id="video-background">
    <source src="/videos/beach.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<head>
    <title>Internal Site Project</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {{-- Fonts, CSS --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div class="wrapper d-flex align-items-stretch">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="custom-menu">
                <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
            </div>
            <div class="img bg-wrap text-center py-4" style="background-image: url('{{ asset('images/bg_1.jpg') }}');">
                <div class="user-logo">
                    <div class="img" style="background-image: url('{{ asset('images/profile1.jpg') }}');"></div>
                    @auth
                    <h3>{{ Auth::user()->name }}</h3>
                    @else
                    <h3>Guest</h3>
                    @endauth
                </div>
            </div>
            <ul class="list-unstyled components mb-5">
                <li class="active">
                    <a href="{{ route('dashboard') }}"><span class="fa fa-home mr-3"></span> Home</a>
                </li>
                <li>
                    <a href="#departmentsSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <span class="fa fa-download mr-3"></span> Departments
                    </a>
                    <ul class="collapse list-unstyled" id="departmentsSubmenu">
                        <li><a href="{{ route('departments.accounting') }}">Accounting</a></li>
                        <li><a href="{{ route('departments.hr') }}">HR</a></li>
                        <li><a href="{{ route('departments.operations') }}">Operations</a></li>
                        <li><a href="{{ route('departments.technical') }}">Technical</a></li>
                        <li><a href="{{ route('departments.controlCenter') }}">Control Center</a></li>
                        <li><a href="{{ route('departments.secretary') }}">Secretary</a></li>
                    </ul>
                </li>
                <li><a href="#"><span class="fa fa-gift mr-3"></span> Gift Code</a></li>
                <li><a href="#"><span class="fa fa-trophy mr-3"></span> Top Review</a></li>
                {{-- s --}}
                <li>
                    <a href="{{ route('admin.settings') }}">
                        <span class="fa fa-cog mr-3"></span> Settings (Admin)
                    </a>
                </li>
                {{-- @endhasrole --}}

                <li>
                    <a href="{{ route('support.form') }}">
                        <span class="fa fa-support mr-3"></span> Support
                    </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="fa fa-sign-out mr-3"></span> Sign Out
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Main Page Content -->
        <div id="content" class="p-4 p-md-5 pt-5">
            @yield('content')
            <!-- This is where each page’s content will go -->
        </div>
    </div>

    {{-- jQuery, Bootstrap, and your main.js --}}
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>