<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ====================================
        // CUSTOM BLADE DIRECTIVES FOR IMAGES
        // ====================================
        
        // Livewire::component('admin.product-create', \App\Http\Livewire\Admin\ProductCreate::class);

        // @image('cestino-roma-blue', 'Alt text', ['class' => 'w-full'])
        Blade::directive('image', function ($expression) {
            return "<?php echo App\Helpers\ImageHelper::img($expression); ?>";
        });

        // @responsiveImage('cestino-roma-blue', 'Alt text', ['class' => 'responsive'])
        Blade::directive('responsiveImage', function ($expression) {
            return "<?php echo App\Helpers\ImageHelper::responsiveImg($expression); ?>";
        });

        // @imageUrl('cestino-roma-blue') 
        Blade::directive('imageUrl', function ($expression) {
            return "<?php echo App\Helpers\ImageHelper::url($expression); ?>";
        });
    }
}
