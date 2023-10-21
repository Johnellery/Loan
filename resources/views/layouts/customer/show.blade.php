

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">

            @livewire('navigation-menu')

<div class="bg-white">
    <div class="pt-6">


      <!-- Image gallery -->
      <div class="mx-auto mt-6 max-w-2xl sm:px-6 lg:grid lg:max-w-4xl sm:grid-cols-1 lg:gap-x-8 lg:px-8">
        <div class="aspect-h-4 aspect-w-3 hidden overflow-hidden rounded-xl  lg:block">
            <img src="{{ $bike->getThumbnailUrl() }}" alt="Two each of gray, white, and black shirts laying flat." class="h-full w-full object-contain">
        </div>
      </div>

      <!-- Product info -->
      <div class="mx-auto max-w-2xl px-4 pb-16 pt-10 sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:grid-rows-[auto,auto,1fr] lg:gap-x-8 lg:px-8 lg:pb-24 lg:pt-16">
        <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
          <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl"> {{ $bike->name }}</h1>
        </div>

        <!-- Options -->
        <div class="mt-4 lg:row-span-3 lg:mt-0">
          <h2 class="sr-only">Product information</h2>
          <p class="text-3xl tracking-tight text-gray-900">PHP {{ number_format($bike->price, 2) }}</p>
          <div class="mt-10">
            <h3 class="text-sm font-medium text-gray-900">Interest rate</h3>

            <div class="mt-4">
              <ul role="list" class=" space-y-2 pl-4 text-sm">
                <li class="text-gray-400"><span class="text-gray-600"> {{ $bike->rate }}%</span></li>
              </ul>
            </div>

          </div>
          <div class="mt-10">
            <h3 class="text-sm font-medium text-gray-900">Down Payment</h3>

            <div class="mt-4">
              <ul role="list" class=" space-y-2 pl-4 text-sm">
                <li class="text-gray-400"><span class="text-gray-600">PHP {{ number_format($bike->down, 2) }}</span></li>
              </ul>
            </div>

          </div>
          <a href="{{ route('customer.loan', ['bike_id' => $bikeId]) }}" class="mt-10 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Apply for Loan</a>

          </form>
        </div>
        <div class="mt-10">
            <h3 class="text-sm font-medium text-gray-900">Details</h3>

            <div class="mt-4">
              <ul role="list" class=" space-y-2 pl-4 text-sm">
                <li class="text-gray-400"><span class="text-gray-600">Brand:  {{ $bike->brand }}</span></li>
                <li class="text-gray-400"><span class="text-gray-600">Category:  {{ $bike->category->name }}</span></li>
              </ul>
            </div>
        </div>
        <div class="mt-10">
            <h3 class="text-sm font-medium text-gray-900">Requirements</h3>

            <div class="mt-4">
              <ul role="list" class=" space-y-2 pl-4 text-sm">
                <li class="text-gray-400"><span class="text-gray-600">Valid ID</span></li>
                <li class="text-gray-400"><span class="text-gray-600">Barangay Clearance</span></li>
              </ul>
            </div>
        </div>
        <div class="py-10 lg:col-span-2 lg:col-start-1 lg:border-r lg:border-gray-200 lg:pb-16 lg:pr-8 lg:pt-6">
          <!-- Description and details -->
          <div>
            <h3 class="sr-only">Description</h3>

            <div class="space-y-6">
              <p class="text-base text-gray-900">  {{ $bike->description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

        @stack('modals')

        @livewireScripts
    </body>


