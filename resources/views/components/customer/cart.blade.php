@props(['bike'])

<div {{ $attributes }}>
    <a href="#">
        <div>
            <img class="w-full rounded-xl" src="{{ $post->getThumbnailUrl() }}">
        </div>
    </a>
    <div class="mt-3">
        <div class="flex items-center mb-2 gap-x-2">
            @if ($category = $bike->category()->first())
                <x-badge wire:navigate href="{{ route('posts.index', ['category' => $category->price]) }}"
                    :textColor="$category->text_color" :bgColor="$category->bg_color">
                    {{ $category->name }}
                </x-badge>
            @endif
            <p class="text-gray-500 text-sm">{{ $bike->published_at }}</p>
        </div>
        <a href="#" class="text-xl font-bold text-gray-900">{{ $bike->name }}</a>
    </div>
</div>
