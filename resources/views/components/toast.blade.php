@props([
    'type' => 'info', // success, error, warning, info
    'message' => '',
    'title' => null,
    'duration' => 5000, // milliseconds
    'closable' => true,
    'position' => 'bottom-right', // top-right, top-left, bottom-right, bottom-left
])

@php
    $colors = [
        'success' => [
            'bg' => 'bg-green-500',
            'text' => 'text-white',
            'icon' => 'fas fa-check-circle',
            'title' => 'Success!'
        ],
        'error' => [
            'bg' => 'bg-red-500',
            'text' => 'text-white',
            'icon' => 'fas fa-exclamation-circle',
            'title' => 'Error!'
        ],
        'warning' => [
            'bg' => 'bg-yellow-500',
            'text' => 'text-gray-900',
            'icon' => 'fas fa-exclamation-triangle',
            'title' => 'Warning!'
        ],
        'info' => [
            'bg' => 'bg-blue-500',
            'text' => 'text-white',
            'icon' => 'fas fa-info-circle',
            'title' => 'Info'
        ]
    ];
    
    $positions = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
    ];
    
    $color = $colors[$type] ?? $colors['info'];
    $position = $positions[$position] ?? $positions['bottom-right'];
    $title = $title ?? $color['title'];
@endphp

<div x-data="{ show: false }" 
     x-init="
        $nextTick(() => {
            setTimeout(() => show = true, 100);
            
            @if($duration > 0)
                setTimeout(() => {
                    show = false;
                    setTimeout(() => $el.remove(), 300);
                }, {{ $duration }});
            @endif
        })
     "
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed {{ $position }} z-50 w-80">
    <div class="relative {{ $color['bg'] }} {{ $color['text'] }} rounded-lg shadow-lg overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="{{ $color['icon'] }} text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    @if($title)
                        <p class="text-sm font-medium">{{ $title }}</p>
                    @endif
                    <p class="mt-1 text-sm">{{ $message }}</p>
                </div>
                @if($closable)
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false; setTimeout(() => $el.remove(), 300);" 
                                class="inline-flex text-gray-300 hover:text-white focus:outline-none">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
        @if($duration > 0)
            <div class="h-1 bg-black bg-opacity-20">
                <div x-data="{ width: 100 }" 
                     x-init="
                        const duration = {{ $duration }};
                        const start = Date.now();
                        const animate = () => {
                            const elapsed = Date.now() - start;
                            const remaining = Math.max(0, duration - elapsed);
                            width = (remaining / duration) * 100;
                            if (remaining > 0) {
                                requestAnimationFrame(animate);
                            }
                        };
                        requestAnimationFrame(animate);
                     "
                     :style="`width: ${width}%`"
                     class="h-full {{ $type === 'success' ? 'bg-green-400' : ($type === 'error' ? 'bg-red-400' : ($type === 'warning' ? 'bg-yellow-400' : 'bg-blue-400')) }}">
                </div>
            </div>
        @endif
    </div>
</div>