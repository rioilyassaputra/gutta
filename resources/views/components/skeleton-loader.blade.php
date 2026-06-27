<div class="animate-pulse space-y-4">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-0 border-2 border-white/10">
        @for($i = 0; $i < 8; $i++)
            <div class="border-r border-b border-white/10">
                <div class="aspect-square skeleton"></div>
                <div class="p-4">
                    <div class="h-2 skeleton w-1/3 mb-2"></div>
                    <div class="h-4 skeleton w-4/5 mb-3"></div>
                    <div class="h-5 skeleton w-1/2"></div>
                </div>
            </div>
        @endfor
    </div>
</div>
