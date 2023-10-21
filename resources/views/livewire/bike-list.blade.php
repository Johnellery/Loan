
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bisikleta Bike Shop</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">

    <div class=" px-3 lg:px-7 py-6">
        {{-- @include('layouts.script') --}}
        {{-- @include('layouts.customer.category') --}}
        <div class="t-5 grid grid-cols-3 gap-4">
            @foreach($bikes as $bike)
                @include('livewire.newcart', ['bike' => $bike])
            @endforeach
        </div>
        <div class="my-3 pagination-container">
            {{ $bikes->links() }}
        </div>
        @include('layouts.customer.footer')
        @livewireScripts
    </div>

</body>
