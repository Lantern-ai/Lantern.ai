<!DOCTYPE html>
<html lang="en" x-data="heroPage()">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Script Analyzer (daisyUI)</title>

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

        html[data-theme='nord'],
        html[data-theme='dracula'] {
            --rounded-btn: 1rem;
            --rounded-box: 1rem;
        }

        .theme-toggle-fixed {
            position: fixed;
            bottom: 1.5rem;
            left: 1.5rem;
            z-index: 50;
        }
    </style>
</head>
<body :data-theme="isDarkMode ? 'dracula' : 'nord'">

<div class="hero min-h-screen bg-base-200">
    <div class="hero-content text-center">
        <div class="max-w-md">
            <h1 class="text-5xl font-bold flex items-center justify-center gap-4">
                <i class="bi bi-file-earmark-text-fill text-primary"></i>
                <span>Script Analyzer</span>
            </h1>
            <p class="py-6">Your intelligent assistant for analyzing and understanding scripts. Log in or register to get started.</p>
            <div class="flex justify-center gap-4">
                <a href="{{route('loginForm')}}" class="btn btn-primary">Login</a>
                <a href="{{route('registerForm')}}" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </div>
</div>

<label class="swap btn btn-ghost theme-toggle-fixed">
    <input type="checkbox" x-model="isDarkMode" />
    <span class="swap-on flex items-center gap-2">
        <i class="bi bi-moon-stars-fill text-xl"></i>
        <span>Dark Mode</span>
    </span>
    <span class="swap-off flex items-center gap-2">
        <i class="bi bi-sun-fill text-xl"></i>
        <span>Light Mode</span>
    </span>
</label>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('heroPage', () => ({
            isDarkMode: (function () {
                const s = localStorage.getItem('docs_theme_dark');
                if (s === 'true') return true;
                if (s === 'false') return false;
                return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            })(),

            init() {
                this.applyTheme();
                this.$watch('isDarkMode', (val) => {
                    localStorage.setItem('docs_theme_dark', val ? 'true' : 'false');
                    this.applyTheme();
                });
            },

            applyTheme() {
                document.documentElement.setAttribute('data-theme', this.isDarkMode ? 'dracula' : 'nord');
            },
        }));
    });
</script>

</body>
</html>
