<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboardpage/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
<script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles

{{-- </head>

<header class="flex items-center justify-between py-3 px-6 border-b border-gray-100">
    <div id="header-left" class="flex items-center">
        <div class="text-gray-800 font-semibold">
            <div class="logo-and-text">
                <img src="{{ asset('storage/dashboardpage/logo.png') }}" alt="Bisikleta" class="logo">
                <span class="text-yellow-500 text-xl">Bisikleta Bike Shop</span>
            </div>
        </div>
        <div class="top-menu ml-10">
            <ul class="flex space-x-4">
                <li>
                    <a id="home-link" class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500"
                        href="http://127.0.0.1:8000" onclick="markActive('home-link')">
                        Home
                    </a>
                </li>

                <li>
                    <a class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500"
                        href="http://127.0.0.1:8000/blog" onclick="markActive('login-link')">
                        About Us
                    </a>
                </li>

                <li>
                    <a class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500"
                        href="http://127.0.0.1:8000/blog" onclick="markActive('login-link')">
                        Contact Us
                    </a>
                </li>

                <li>
                    <a class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500"
                        href="http://127.0.0.1:8000/blog" onclick="markActive('login-link')">
                        Terms
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div id="header-right" class="flex items-center md:space-x-6">
        <div class="flex space-x-5">
                <a id="login-link" class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500"
                    href="http://127.0.0.1:8000/login" onclick="markActive('login-link')">
                    Login
                </a>
                <a id="register-link" class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500"
                    href="http://127.0.0.1:8000/register" onclick="markActive('register-link')">
                    Register
                </a>
        </div>
    </div>
</header>

<script>
    function markActive(linkId) {
        // Remove the 'active-link' class from all links
        const links = document.querySelectorAll('.active-link');
        links.forEach(link => {
            link.classList.remove('active-link');
        });

        // Add the 'active-link' class to the clicked link
        const clickedLink = document.getElementById(linkId);
        clickedLink.classList.add('active-link');
    }
</script> --}}
{{-- <header> --}}
{{--
    <nav class="fixed top-0 left-0 z-20 w-full border-b border-gray-200 bg-white py-2.5 px-6 sm:px-4">
        <div class="container mx-auto flex max-w-6xl flex-wrap items-center justify-between">
          <a href="#" class="flex items-center">
            <img src="{{ asset('storage/dashboardpage/logo.png') }}" alt="Bisikleta" class="logo">
            <span class="text-yellow-500 text-xl">Bisikleta Bike Shop</span>
          </a>
          <div class="mt-2 sm:mt-0 sm:flex md:order-2">
            <!-- Login Button -->
            <a type="button"  href="http://127.0.0.1:8000/login" class="ue-700 py-1.5 px-6 text-center text-sm font-medium text-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 md:inline-block rounded-lg">Login</a>
            <a type="button"  href="http://127.0.0.1:8000/register" class="rounde mr-3 hidden bg-blue-700 py-1.5 px-6 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 md:mr-0 md:inline-block rounded-lg">Register</a>
            <!-- Register Button -->
            <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 md:hidden" aria-controls="navbar-sticky" aria-expanded="false">
              <span class="sr-only">Open main menu</span>
              <svg class="h-6 w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
            </button>
          </div>
          <div class="hidden w-full items-center justify-between md:order-1 md:flex md:w-auto" id="navbar-sticky">
            <ul class="mt-4 flex flex-col rounded-lg border border-gray-100 bg-gray-50 p-4 md:mt-0 md:flex-row md:space-x-8 md:border-0 md:bg-white md:text-sm md:font-medium">
              <li>
                <a href="http://127.0.0.1:8000" class="block rounded bg-blue-700 py-2 pl-3 pr-4 text-white md:bg-transparent md:p-0 md:text-blue-700" aria-current="page">Home</a>
              </li>
              <li>
                <a href="#" class="block rounded py-2 pl-3 pr-4 text-gray-700 hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-blue-700">About</a>
              </li>
              <li>
                <a href="#" class="block rounded py-2 pl-3 pr-4 text-gray-700 hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-blue-700">Services</a>
              </li>
              <li>
                <a href="#" class="block rounded py-2 pl-3 pr-4 text-gray-700 hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-blue-700">Contact</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header> --}}
