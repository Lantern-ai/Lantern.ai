<!DOCTYPE html>
<html lang="en" x-data="docsDashboard()">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Docs (daisyUI)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .text-theme {
            color: hsl(var(--p));
        }

        html[data-theme='nord'],
        html[data-theme='dracula'] {
            --rounded-btn: 0.5rem;
        }

        .theme-toggle-fixed {
            position: fixed;
            bottom: 1.5rem;
            left: 1.5rem;
            z-index: 50;
        }

        .navbar,
        .drawer-side .menu {
            border: none;
        }

        /* Smooth transitions */
        .card {
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>
@yield('style')

</head>
<body>
<div class="drawer lg:drawer-open">
    <input id="sidebar-drawer" type="checkbox" class="drawer-toggle">

    <!-- Main Content -->
    <div class="drawer-content flex flex-col bg-base-200">

        <!-- Navbar -->
        <header class="navbar bg-base-100 shadow-sm sticky top-0 z-30 px-4">
            <div class="navbar-start">
                <label for="sidebar-drawer" class="btn btn-ghost btn-circle lg:hidden">
                    <i class="bi bi-list text-xl"></i>
                </label>
                <div class="flex items-center ml-2">
                    <i class="bi bi-file-earmark-text-fill text-3xl text-theme"></i>
                    <span class="text-xl ml-2 font-semibold hidden sm:inline">Script Analyzer</span>
                </div>
            </div>

            <div class="navbar-end">
                <!-- User Dropdown -->
                <div class="dropdown dropdown-end ml-2">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img alt="User Avatar" src="https://via.placeholder.com/40">
                        </div>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Register</a></li>
                        <li><hr class="my-1"></li>
                        <li><a href="{{route("logout")}}">Logout </a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
@yield('content')
    </div>

    <!-- Sidebar -->
    <aside class="drawer-side">
        <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu p-4 w-64 min-h-full bg-base-100 text-base-content gap-1">
            <li class="menu-title">Navigation</li>
            <li><a href="#" class="active"><i class="bi bi-folder2-open text-lg"></i> My Documents</a></li>
            <li><a href="#"><i class="bi bi-plus-square text-lg"></i> New Document</a></li>
            <li><a href="#"><i class="bi bi-star text-lg"></i> Starred</a></li>
            <li><a href="#"><i class="bi bi-trash text-lg"></i> Trash</a></li>
            <li><a href="#"><i class="bi bi-gear text-lg"></i> Settings</a></li>
        </ul>
    </aside>
</div>

<!-- Theme Toggle Button -->
<label class="swap swap-rotate btn btn-ghost btn-circle theme-toggle-fixed">
    <input type="checkbox" x-model="isDarkMode">
    <i class="swap-on bi bi-moon-stars-fill text-2xl"></i>
    <i class="swap-off bi bi-sun-fill text-2xl"></i>
</label>
@yield('script')
</body>
</html>
