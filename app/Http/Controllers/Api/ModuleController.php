<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModuleService;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * Display a listing of modules
     */
    public function index()
    {
        try {
            $modules = $this->moduleService->getAllModules();
            
            return response()->json([
                'success' => true,
                'data' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created module
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:modules,slug',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer',
            'is_core' => 'nullable|boolean',
        ]);

        try {
            $module = $this->moduleService->createModule($validated);
            
            return response()->json([
                'success' => true,
                'message' => __('Module created successfully.'),
                'data' => $module
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified module
     */
    public function show(string $id)
    {
        try {
            $module = $this->moduleService->getModuleWithFeatures($id);
            
            return response()->json([
                'success' => true,
                'data' => $module
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified module
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:modules,slug,' . $id,
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer',
            'is_core' => 'nullable|boolean',
        ]);

        try {
            $module = $this->moduleService->updateModule($id, $validated);
            
            return response()->json([
                'success' => true,
                'message' => __('Module updated successfully.'),
                'data' => $module
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified module
     */
    public function destroy(string $id)
    {
        try {
            $this->moduleService->deleteModule($id);
            
            return response()->json([
                'success' => true,
                'message' => __('Module deleted successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
