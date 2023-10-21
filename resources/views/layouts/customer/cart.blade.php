
@props(['bike'])

<div class="relative flex flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md hover:shadow-lg">
    <div class="relative mx-4 mt-4 h-0 overflow-hidden pb-[100%] rounded-xl bg-white bg-clip-border border border-gray-300 text-gray-700">
        <img src="{{ $bike->getThumbnailUrl() }}" class="absolute h-full w-full object-cover"/>
    </div>

    <div class="p-6">
        <div class="mb-2 flex items-center justify-between">
            <p class="block font-sans text-base font-medium leading-relaxed text-blue-gray-900 antialiased">
                {{ $bike->name }}
            </p>
            {{-- <p class="block font-sans text-xs font-medium leading-relaxed text-orange-500 antialiased">

            </p> --}}
        </div>
        <p class="block font-sans text-sm font-normal leading-normal text-orange-500 antialiased opacity-75">
            PHP {{ number_format($bike->price, 2) }}
        </p>
    </div>
    <div class="p-6 pt-0">
        <button class="block w-full select-none rounded-lg bg-orange-500 py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase text-white transition-all hover:scale-105 focus:scale-105 focus:opacity-[0.85] active:scale-100 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
            Add to Cart
        </button>
    </div>
</div>
