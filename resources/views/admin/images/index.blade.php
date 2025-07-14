<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestione Immagini
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Livewire Gallery Component -->
            <livewire:admin.image-gallery />
        </div>
    </div>

    <!-- Alpine.js per toast messages -->
    <script>
        // Listen for Livewire events
        document.addEventListener('livewire:init', () => {
            Livewire.on('image-updated', (event) => {
                console.log('Image updated');
            });
            
            Livewire.on('images-uploaded', (event) => {
                console.log('Images uploaded');
            });
            
            Livewire.on('image-optimized', (event) => {
                console.log('Image optimized');
            });
        });
    </script>

    @push('styles')
    <style>
        /* Responsive grid improvements */
        @media (max-width: 640px) {
            .grid-cols-2 {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
        }
        
        /* Image hover effects */
        .image-card:hover .group-hover\:opacity-100 {
            opacity: 1;
        }
        
        /* Loading states */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
    @endpush
</x-app-layout>