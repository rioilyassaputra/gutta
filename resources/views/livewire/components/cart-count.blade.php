<span>
    @if($count > 0)
        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-black bg-violet-600 text-white rounded-none">
            {{ $count > 9 ? '9+' : $count }}
        </span>
    @endif
</span>
