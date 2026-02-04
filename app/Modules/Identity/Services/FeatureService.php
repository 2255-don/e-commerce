<?php

namespace Modules\Identity\Services;

use App\Models\Feature;
use App\Models\Module;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class FeatureService
{
    /**
     * Get all features with their module
     */
    public function getAllFeatures()
    {
        return Feature::with('module')->get();
    }

    /**
     * Get features filtered by module
     */
    public function getFeaturesByModule(string $moduleId)
    {
        return Feature::forModule($moduleId)->get();
    }

    /**
     * Get features filtered by type
     */
    public function getFeaturesByType(string $type)
    {
        return Feature::ofType($type)->with('module')->get();
    }

    /**
     * Get a single feature with relationships
     */
    public function getFeature(string $id)
    {
        return Feature::with(['module', 'permissions'])->findOrFail($id);
    }

    /**
     * Create a new feature
     */
    public function createFeature(array $data)
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Feature::create($data);
    }

    /**
     * Update an existing feature
     */
    public function updateFeature(string $id, array $data)
    {
        $feature = Feature::findOrFail($id);
        $feature->update($data);
        
        return $feature;
    }

    /**
     * Delete a feature
     */
    public function deleteFeature(string $id)
    {
        $feature = Feature::findOrFail($id);
        $feature->delete();
    }

    /**
     * Scan and register features from routes and views
     */
    public function scanAndRegister(bool $fresh = false): array
    {
        if ($fresh) {
            Feature::truncate();
        }

        $stats = [
            'routes_scanned' => 0,
            'components_scanned' => 0,
            'new_features' => 0,
            'updated_features' => 0,
        ];

        // Scan routes
        foreach (Route::getRoutes() as $route) {
            if ($name = $route->getName()) {
                $stats['routes_scanned']++;
                
                $moduleSlug = $this->detectModuleFromSlug($name);
                $module = Module::where('slug', $moduleSlug)->first();
                
                if (!$module) {
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
                    $stats['new_features']++;
                } else {
                    $stats['updated_features']++;
                }
            }
        }

        // Scan Blade views
        $bladeFiles = File::allFiles(resource_path('views'));
        
        foreach ($bladeFiles as $file) {
            $content = $file->getContents();
            
            preg_match_all('/<x-feature-(?:button|section|link)\s+feature="([^"]+)"/', $content, $matches);
            
            foreach (array_unique($matches[1] ?? []) as $featureSlug) {
                $stats['components_scanned']++;
                
                $moduleSlug = $this->detectModuleFromSlug($featureSlug);
                $module = Module::where('slug', $moduleSlug)->first();
                
                if (!$module) {
                    continue;
                }

                $feature = Feature::updateOrCreate(
                    ['slug' => $featureSlug],
                    [
                        'module_id' => $module->id,
                        'name' => $this->generateNameFromSlug($featureSlug),
                        'type' => 'ui_element',
                        'metadata' => [
                            'file' => $file->getRelativePath() . '/' . $file->getFilename(),
                        ],
                    ]
                );

                if ($feature->wasRecentlyCreated) {
                    $stats['new_features']++;
                } else {
                    $stats['updated_features']++;
                }
            }
        }

        $stats['total_features'] = Feature::count();

        return $stats;
    }

    /**
     * Detect module from feature slug
     */
    private function detectModuleFromSlug(string $slug): string
    {
        $parts = explode('.', $slug);
        return $parts[0];
    }

    /**
     * Generate human-readable name from slug
     */
    private function generateNameFromSlug(string $slug): string
    {
        $parts = explode('.', $slug);
        if (count($parts) > 1) {
            array_shift($parts);
            $nameWithoutModule = implode(' ', $parts);
        } else {
            $nameWithoutModule = $slug;
        }
        
        return Str::headline($nameWithoutModule);
    }

    /**
     * Check for orphaned features (in DB but not in code)
     */
    public function checkOrphanedFeatures(): array
    {
        // Get all features from DB
        $dbFeatures = Feature::pluck('slug')->toArray();
        
        // Get features from routes
        $routeFeatures = [];
        foreach (Route::getRoutes() as $route) {
            if ($name = $route->getName()) {
                $routeFeatures[] = $name;
            }
        }
        
        // Get features from views
        $viewFeatures = [];
        $bladeFiles = File::allFiles(resource_path('views'));
        foreach ($bladeFiles as $file) {
            $content = $file->getContents();
            preg_match_all('/<x-feature-(?:button|section|link)\s+feature="([^"]+)"/', $content, $matches);
            $viewFeatures = array_merge($viewFeatures, $matches[1] ?? []);
        }
        
        $codeFeatures = array_unique(array_merge($routeFeatures, $viewFeatures));
        
        return [
            'orphaned' => array_diff($dbFeatures, $codeFeatures),
            'missing' => array_diff($codeFeatures, $dbFeatures),
        ];
    }
}
