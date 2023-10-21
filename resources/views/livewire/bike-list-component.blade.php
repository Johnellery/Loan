<div>
    <div class=" px-3 lg:px-7 py-6">
        @include('layouts.script')
        @include('layouts.customer.category')
        <div class="t-5 grid grid-cols-3 gap-2">
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
</div>
