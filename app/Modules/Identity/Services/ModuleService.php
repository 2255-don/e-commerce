<?php

namespace Modules\Identity\Services;

use App\Models\Module;
use Illuminate\Support\Str;

class ModuleService
{
    /**
     * Get all modules ordered by their order field
     */
    public function getAllModules()
    {
        return Module::ordered()->withCount('features')->get();
    }

    /**
     * Get a single module with its features
     */
    public function getModuleWithFeatures(string $id)
    {
        return Module::with('features')->findOrFail($id);
    }

    /**
     * Create a new module
     */
    public function createModule(array $data)
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Module::create($data);
    }

    /**
     * Update an existing module
     */
    public function updateModule(string $id, array $data)
    {
        $module = Module::findOrFail($id);
        $module->update($data);
        
        return $module;
    }

    /**
     * Delete a module and all its features
     */
    public function deleteModule(string $id)
    {
        $module = Module::findOrFail($id);
        
        // Check if module is core (cannot be deleted)
        if ($module->is_core) {
            throw new \Exception(__('Core modules cannot be deleted.'));
        }

        $module->delete();
    }

    /**
     * Reorder modules
     */
    public function reorderModules(array $orderedIds)
    {
        foreach ($orderedIds as $order => $id) {
            Module::where('id', $id)->update(['order' => $order]);
        }
    }

    /**
     * Get modules grouped by core/non-core
     */
    public function getModulesGrouped()
    {
        return [
            'core' => Module::core()->ordered()->get(),
            'custom' => Module::where('is_core', false)->ordered()->get(),
        ];
    }
}
