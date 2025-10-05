<!DOCTYPE html>
<html lang="en" x-data="loginPage()">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Login - Docs (daisyUI)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>

    <style>
        html {
            font-size: 18px;
        }

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
    <div class="hero-content flex-col lg:flex-row-reverse gap-12">
        <div class="w-full max-w-sm lg:max-w-xl mx-auto rounded-xl  aspect-square">

            <template x-if="!isDarkMode">
                <dotlottie-wc
                    src="https://lottie.host/cd3ee390-60fc-41ae-9f9c-757cc253b80c/WLjtZjUAIJ.lottie"
                    style="width: 500px; height: 500px;"
                    autoplay

                ></dotlottie-wc>
            </template>

            <template x-if="isDarkMode">
                <dotlottie-wc
                    src="https://lottie.host/53beed0b-f6dd-4bc5-907b-3e076bcfaef4/wOzGXW3owx.lottie"
                    style="width: 500px; height: 500px;"
                    autoplay

                ></dotlottie-wc>
            </template>
        </div>
        <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100 rounded-xl">
            <form class="card-body" method="post" action="{{route("login")}}">
                <h1 class="text-3xl font-bold text-center mb-4">Login</h1>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>

                    </label>
                    <input x-model="email" name="email" type="email" placeholder="email" class="input input-bordered" required />
                    @error('email')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input x-model="password" name="password" type="password" placeholder="password" class="input input-bordered" required />
                    @error('password')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                    <label class="label">
                        <a href="#" class="label-text-alt link link-hover">Forgot password?</a>
                    </label>
                    @csrf
                </div>
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
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
        Alpine.data('loginPage', () => ({
            isDarkMode: (function () {
                const s = localStorage.getItem('docs_theme_dark');
                if (s === 'true') return true;
                if (s === 'false') return false;
                return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            })(),

            email: '',
            password: '',

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

            handleLogin() {
                if (!this.email || !this.password) {
                    alert('Please enter both email and password.');
                    return;
                }
                console.log('Attempting login with:');
                console.log('Email:', this.email);
                console.log('Password:', this.password);
                alert('Login attempt logged to console. In a real app, you would redirect from here.');
            }
        }));
    });
</script>

</body>
</html>
