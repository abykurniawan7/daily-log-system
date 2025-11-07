<div x-data="{ langMenuOpen: false }" class="relative">
    <button @click="langMenuOpen = !langMenuOpen" 
            type="button" 
            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 transition-colors"
            style="color: var(--primary-green);">
        
        {{-- Flag Icon --}}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
        </svg>
        
        {{-- Current Language --}}
        <span class="hidden sm:inline-block font-medium">
            {{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}
        </span>
        
        {{-- Dropdown Arrow --}}
        <svg class="w-4 h-4 transition-transform" :class="langMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    {{-- Dropdown Menu --}}
    <div x-show="langMenuOpen" 
         @click.away="langMenuOpen = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none;">
        
        <div class="py-1">
            {{-- Indonesian --}}
            <a href="{{ url('/set-locale/id') }}" 
               class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 transition-colors {{ app()->getLocale() === 'id' ? 'bg-green-50 font-semibold' : 'text-gray-700' }}"
               style="{{ app()->getLocale() === 'id' ? 'color: var(--primary-green);' : '' }}">
                
                <span class="text-2xl">🇮🇩</span>
                <div class="flex-1">
                    <div class="font-medium">Bahasa Indonesia</div>
                    <div class="text-xs text-gray-500">Indonesian</div>
                </div>
                
                @if(app()->getLocale() === 'id')
                    <svg class="w-5 h-5" style="color: var(--primary-green);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </a>
            
            {{-- English --}}
            <a href="{{ url('/set-locale/en') }}" 
               class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 transition-colors {{ app()->getLocale() === 'en' ? 'bg-green-50 font-semibold' : 'text-gray-700' }}"
               style="{{ app()->getLocale() === 'en' ? 'color: var(--primary-green);' : '' }}">
                
                <span class="text-2xl">🇬🇧</span>
                <div class="flex-1">
                    <div class="font-medium">English</div>
                    <div class="text-xs text-gray-500">Inggris</div>
                </div>
                
                @if(app()->getLocale() === 'en')
                    <svg class="w-5 h-5" style="color: var(--primary-green);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </a>
        </div>
        
        {{-- Info Footer --}}
        <div class="border-t border-gray-100 px-4 py-2 bg-gray-50 rounded-b-lg">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Auto-detected from browser</span>
            </div>
        </div>
    </div>
</div>