{{-- Toast Notification Component - Pure JavaScript Version --}}
@if (session('success') || session('error') || session('warning') || session('info'))
<div id="toast-container" class="fixed top-4 right-4 z-50 max-w-sm w-full px-4">
    <div id="toast" class="bg-white rounded-lg shadow-lg overflow-hidden border-l-4 transform transition-all duration-300 translate-x-full opacity-0
        @if(session('success')) border-green-500
        @elseif(session('error')) border-red-500
        @elseif(session('warning')) border-yellow-500
        @else border-blue-500
        @endif">
        
        <div class="p-4">
            <div class="flex items-start">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    @if(session('success'))
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(session('error'))
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(session('warning'))
                        <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @else
                        <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>
                
                <!-- Message -->
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        @if(session('success'))
                            {{ session('success') }}
                        @elseif(session('error'))
                            {{ session('error') }}
                        @elseif(session('warning'))
                            {{ session('warning') }}
                        @else
                            {{ session('info') }}
                        @endif
                    </p>
                </div>
                
                <!-- Close Button -->
                <div class="ml-4 flex-shrink-0">
                    <button onclick="closeToast()" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="h-1 bg-gray-200">
            <div id="toast-progress" class="h-full transition-all ease-linear
                @if(session('success')) bg-green-500
                @elseif(session('error')) bg-red-500
                @elseif(session('warning')) bg-yellow-500
                @else bg-blue-500
                @endif"
                style="width: 100%; transition: width 5s linear;">
            </div>
        </div>
    </div>
</div>

<script>
    // Show toast on page load
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        const progress = document.getElementById('toast-progress');
        
        if (toast) {
            // Show toast with animation
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            }, 100);
            
            // Start progress bar
            setTimeout(() => {
                progress.style.width = '0%';
            }, 100);
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                closeToast();
            }, 5000);
        }
    });
    
    function closeToast() {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            
            // Remove from DOM after animation
            setTimeout(() => {
                const container = document.getElementById('toast-container');
                if (container) {
                    container.remove();
                }
            }, 300);
        }
    }
</script>
@endif