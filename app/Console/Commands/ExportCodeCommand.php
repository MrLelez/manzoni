<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportCodeCommand extends Command
{
    protected $signature = 'manzoni:export-code 
                            {--output=export.txt : Output filename} 
                            {--minify : Minify the code}
                            {--controllers : Export only controllers}
                            {--models : Export only models}
                            {--views : Export only views}
                            {--routes : Export only routes}
                            {--services : Export only services}
                            {--migrations : Export only migrations}
                            {--config : Export only config files}
                            {--all : Export everything (default)}';
    protected $description = 'Export Manzoni project code sections to TXT file';

    public function handle()
    {
        $output = $this->option('output');
        $content = [];
        
        $this->info('🚀 Exporting Manzoni project code...');
        $this->newLine();

        // Header
        $content[] = "# MANZONI PROJECT CODE EXPORT";
        $content[] = "# Generated: " . now()->format('Y-m-d H:i:s');
        $content[] = "# Laravel 11 + Jetstream + TALL Stack";
        $content[] = "# Admin Products Interface + AWS S3 Images";
        $content[] = "";

        // Determine what to export
        $exportAll = !$this->hasSpecificOptions();
        
        if ($exportAll || $this->option('controllers')) {
            $this->exportControllers($content);
        }
        
        if ($exportAll || $this->option('models')) {
            $this->exportModels($content);
        }
        
        if ($exportAll || $this->option('views')) {
            $this->exportViews($content);
        }
        
        if ($exportAll || $this->option('routes')) {
            $this->exportRoutes($content);
        }
        
        if ($exportAll || $this->option('services')) {
            $this->exportServices($content);
        }
        
        if ($exportAll || $this->option('migrations')) {
            $this->exportMigrations($content);
        }
        
        if ($exportAll || $this->option('config')) {
            $this->exportConfig($content);
        }

        // Write to file
        $fullContent = implode("\n", $content);
        File::put(storage_path("app/{$output}"), $fullContent);
        
        $size = File::size(storage_path("app/{$output}"));
        $sizeKb = round($size / 1024, 2);
        
        $this->info("✅ Export completed!");
        $this->line("📁 File: storage/app/{$output}");
        $this->line("📊 Size: {$sizeKb} KB");
        $this->line("🔗 Lines: " . count($content));
        
        return 0;
    }

    private function hasSpecificOptions(): bool
    {
        return $this->option('controllers') || 
               $this->option('models') || 
               $this->option('views') || 
               $this->option('routes') || 
               $this->option('services') || 
               $this->option('migrations') || 
               $this->option('config');
    }

    private function exportControllers(&$content)
    {
        $this->info('📂 Exporting Controllers...');
        
        $controllers = [
            'app/Http/Controllers/Admin/AdminDashboardController.php',
            'app/Http/Controllers/Admin/ProductController.php',
            'app/Http/Controllers/Admin/CategoryController.php',
            'app/Http/Controllers/ImageController.php',
            'app/Http/Controllers/Admin/ImagesController.php',
        ];

        $content[] = "## ===== CONTROLLERS =====";
        
        foreach ($controllers as $file) {
            if (File::exists(base_path($file))) {
                $content[] = "### FILE: {$file}";
                $content[] = "```php";
                $content[] = File::get(base_path($file)); // CONTENUTO COMPLETO
                $content[] = "```";
                $content[] = "";
                $this->line("  ✅ {$file}");
            } else {
                $this->line("  ❌ {$file} (not found)");
            }
        }
    }

    private function exportModels(&$content)
    {
        $this->info('📂 Exporting Models...');
        
        $models = [
            'app/Models/User.php',
            'app/Models/Product.php',
            'app/Models/Category.php',
            'app/Models/ProductVariant.php',
            'app/Models/ProductAccessory.php',
            'app/Models/Image.php',
            'app/Models/Tag.php',
            'app/Models/Translation.php',
        ];

        $content[] = "## ===== MODELS =====";
        
        foreach ($models as $file) {
            if (File::exists(base_path($file))) {
                $content[] = "### FILE: {$file}";
                $content[] = "```php";
                if ($this->option('minify')) {
                    $content[] = $this->minifyPhp(File::get(base_path($file)));
                } else {
                    $content[] = File::get(base_path($file));
                }
                $content[] = "```";
                $content[] = "";
                $this->line("  ✅ {$file}");
            }
        }
                $content[] = "### FILE: {$file}";
                $content[] = $this->minifyPhp(File::get(base_path($file)));
                $content[] = "";
                $this->line("  ✅ {$file}");
            }
        }
    }

    private function exportViews(&$content)
    {
        $this->info('📂 Exporting Views...');
        
        $views = [
            'resources/views/admin/dashboard.blade.php',
            'resources/views/admin/users/index.blade.php',
            'resources/views/admin/users/create.blade.php',
            'resources/views/admin/products/index.blade.php',
            'resources/views/admin/products/create.blade.php',
            'resources/views/admin/products/edit.blade.php',
            'resources/views/admin/products/show.blade.php',
        ];

        $content[] = "## ===== VIEWS =====";
        
        foreach ($views as $file) {
            if (File::exists(base_path($file))) {
                $content[] = "### FILE: {$file}";
                $content[] = "```blade";
                if ($this->option('minify')) {
                    $content[] = $this->minifyBlade(File::get(base_path($file)));
                } else {
                    $content[] = File::get(base_path($file));
                }
                $content[] = "```";
                $content[] = "";
                $this->line("  ✅ {$file}");
            } else {
                $this->line("  ❌ {$file} (not found)");
            }
        }
    }

    private function exportRoutes(&$content)
    {
        $this->info('📂 Exporting Routes...');
        
        $routeFiles = [
            'routes/web.php',
            'routes/api.php',
        ];

        $content[] = "## ===== ROUTES =====";
        
        foreach ($routeFiles as $file) {
            if (File::exists(base_path($file))) {
                $content[] = "### FILE: {$file}";
                $content[] = "```php";
                if ($this->option('minify')) {
                    $content[] = $this->minifyPhp(File::get(base_path($file)));
                } else {
                    $content[] = File::get(base_path($file));
                }
                $content[] = "```";
                $content[] = "";
                $this->line("  ✅ {$file}");
            }
        }
    }

    private function exportServices(&$content)
    {
        $this->info('📂 Exporting Services...');
        
        $services = [
            'app/Services/UserRoleService.php',
            'app/Services/ImageService.php',
        ];

        $content[] = "## ===== SERVICES =====";
        
        foreach ($services as $file) {
            if (File::exists(base_path($file))) {
                $content[] = "### FILE: {$file}";
                $content[] = "```php";
                if ($this->option('minify')) {
                    $content[] = $this->minifyPhp(File::get(base_path($file)));
                } else {
                    $content[] = File::get(base_path($file));
                }
                $content[] = "```";
                $content[] = "";
                $this->line("  ✅ {$file}");
            }
        }
    }

    private function exportMigrations(&$content)
    {
        $this->info('📂 Exporting Recent Migrations...');
        
        $migrationPath = database_path('migrations');
        $migrations = File::glob($migrationPath . '/*.php');
        
        // Get recent migrations (last 10)
        $recentMigrations = array_slice(array_reverse($migrations), 0, 10);

        $content[] = "## ===== RECENT MIGRATIONS =====";
        
        foreach ($recentMigrations as $file) {
            $filename = basename($file);
            $content[] = "### FILE: database/migrations/{$filename}";
            $content[] = "```php";
            if ($this->option('minify')) {
                $content[] = $this->minifyPhp(File::get($file));
            } else {
                $content[] = File::get($file);
            }
            $content[] = "```";
            $content[] = "";
            $this->line("  ✅ {$filename}");
        }
    }

    private function exportConfig(&$content)
    {
        $this->info('📂 Exporting Config...');
        
        $configs = [
            'config/app.php',
            'config/database.php',
            'config/filesystems.php',
            'config/permission.php',
        ];

        $content[] = "## ===== CONFIG =====";
        
        foreach ($configs as $file) {
            if (File::exists(base_path($file))) {
                $content[] = "### FILE: {$file}";
                $content[] = "```php";
                if ($this->option('minify')) {
                    $content[] = $this->minifyPhp(File::get(base_path($file)));
                } else {
                    $content[] = File::get(base_path($file));
                }
                $content[] = "```";
                $content[] = "";
                $this->line("  ✅ {$file}");
            }
        }

        // Add .env example
        if (File::exists(base_path('.env.example'))) {
            $content[] = "### FILE: .env.example";
            $content[] = File::get(base_path('.env.example'));
            $content[] = "";
            $this->line("  ✅ .env.example");
        }
    }

    private function minifyPhp(string $code): string
    {
        // Remove comments and extra whitespace
        $code = preg_replace('/\/\*[\s\S]*?\*\//', '', $code); // Remove /* */ comments
        $code = preg_replace('/\/\/.*$/m', '', $code); // Remove // comments
        $code = preg_replace('/^\s*\*.*$/m', '', $code); // Remove docblock lines
        $code = preg_replace('/^\s*$/m', '', $code); // Remove empty lines
        $code = preg_replace('/\s+/', ' ', $code); // Multiple spaces to single
        $code = preg_replace('/\s*{\s*/', '{', $code); // Clean braces
        $code = preg_replace('/\s*}\s*/', '}', $code);
        $code = preg_replace('/;\s*/', ';', $code);
        
        return trim($code);
    }

    private function minifyBlade(string $code): string
    {
        // Remove HTML comments
        $code = preg_replace('/<!--[\s\S]*?-->/', '', $code);
        // Remove extra whitespace but preserve blade syntax
        $code = preg_replace('/\s+/', ' ', $code);
        $code = preg_replace('/>\s+</', '><', $code);
        
        return trim($code);
    }
}