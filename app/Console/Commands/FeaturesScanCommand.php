<?php

namespace App\Console\Commands;

use Modules\Identity\Entities\Feature;
use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class FeaturesScanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:scan {--fresh : Delete all existing features before scanning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan routes and Blade views to auto-register features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->warn('Deleting all existing features...');
            Feature::truncate();
        }

        $this->info('Starting features scan...');
        
        $routeCount = 0;
        $componentCount = 0;
        $newFeatures = 0;
        $updatedFeatures = 0;

        // Scan routes
        $this->info('📍 Scanning routes...');
        foreach (Route::getRoutes() as $route) {
            if ($name = $route->getName()) {
                $routeCount++;
                
                $moduleSlug = $this->detectModuleFromSlug($name);
                $module = Module::where('slug', $moduleSlug)->first();
                
                if (!$module) {
                    $this->warn("  ⚠ Module '{$moduleSlug}' not found for route '{$name}', skipping...");
                    continue;
                }

                $feature = Feature::updateOrCreate(
                    ['slug' => $name],
                    [
                        'module_id' => $module->id,
                        'name' => $this->generateNameFromSlug($name),
                        'type' => 'route',
                        'metadata' => [
                            'path' => $route->uri(),
                            'methods' => $route->methods(),
                        ],
                    ]
                );

                if ($feature->wasRecentlyCreated) {
                    $newFeatures++;
                    $this->line("  ✓ Created: {$name}");
                } else {
                    $updatedFeatures++;
                }
            }
        }

        // Scan Blade views
        $this->info('📄 Scanning Blade views...');
        $bladeFiles = File::allFiles(resource_path('views'));
        
        foreach ($bladeFiles as $file) {
            $content = $file->getContents();
            
            // Match <x-feature-button feature="slug">
            preg_match_all('/<x-feature-button\s+feature="([^"]+)"/', $content, $buttonMatches);
            // Match <x-feature-section feature="slug">
            preg_match_all('/<x-feature-section\s+feature="([^"]+)"/', $content, $sectionMatches);
            // Match <x-feature-link feature="slug">
            preg_match_all('/<x-feature-link\s+feature="([^"]+)"/', $content, $linkMatches);
            
            $allMatches = array_merge(
                $buttonMatches[1] ?? [],
                $sectionMatches[1] ?? [],
                $linkMatches[1] ?? []
            );
            
            foreach (array_unique($allMatches) as $featureSlug) {
                $componentCount++;
                
                $moduleSlug = $this->detectModuleFromSlug($featureSlug);
                $module = Module::where('slug', $moduleSlug)->first();
                
                if (!$module) {
                    $this->warn("  ⚠ Module '{$moduleSlug}' not found for feature '{$featureSlug}', skipping...");
                    continue;
                }

                $type = 'ui_element';
                if (in_array($featureSlug, $sectionMatches[1] ?? [])) {
                    $type = 'section';
                }

                $feature = Feature::updateOrCreate(
                    ['slug' => $featureSlug],
                    [
                        'module_id' => $module->id,
                        'name' => $this->generateNameFromSlug($featureSlug),
                        'type' => $type,
                        'metadata' => [
                            'file' => $file->getRelativePath() . '/' . $file->getFilename(),
                        ],
                    ]
                );

                if ($feature->wasRecentlyCreated) {
                    $newFeatures++;
                    $this->line("  ✓ Created: {$featureSlug}");
                } else {
                    $updatedFeatures++;
                }
            }
        }

        $this->newLine();
        $this->info('✅ Scan completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Routes scanned', $routeCount],
                ['Components scanned', $componentCount],
                ['New features registered', $newFeatures],
                ['Existing features updated', $updatedFeatures],
                ['Total features in DB', Feature::count()],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Detect module from feature slug
     * Example: "users.create" -> "users"
     */
    private function detectModuleFromSlug(string $slug): string
    {
        $parts = explode('.', $slug);
        return $parts[0];
    }

    /**
     * Generate human-readable name from slug
     * Example: "users.create" -> "Create User"
     */
    private function generateNameFromSlug(string $slug): string
    {
        // Remove module prefix
        $parts = explode('.', $slug);
        if (count($parts) > 1) {
            array_shift($parts);
            $nameWithoutModule = implode(' ', $parts);
        } else {
            $nameWithoutModule = $slug;
        }
        
        return Str::headline($nameWithoutModule);
    }
}
