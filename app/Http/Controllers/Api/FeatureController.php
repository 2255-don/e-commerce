<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FeatureService;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    protected $featureService;

    public function __construct(FeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    /**
     * Display a listing of features
     */
    public function index(Request $request)
    {
        try {
            $moduleId = $request->get('module');
            $type = $request->get('type');

            if ($moduleId) {
                $features = $this->featureService->getFeaturesByModule($moduleId);
            } elseif ($type) {
                $features = $this->featureService->getFeaturesByType($type);
            } else {
                $features = $this->featureService->getAllFeatures();
            }
            
            return response()->json([
                'success' => true,
                'data' => $features
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created feature
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:features,slug',
            'type' => 'required|in:route,action,ui_element,section',
            'description' => 'nullable|string',
        ]);

        try {
            $feature = $this->featureService->createFeature($validated);
            
            return response()->json([
                'success' => true,
                'message' => __('Feature created successfully.'),
                'data' => $feature
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified feature
     */
    public function show(string $id)
    {
        try {
            $feature = $this->featureService->getFeature($id);
            
            return response()->json([
                'success' => true,
                'data' => $feature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified feature
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:features,slug,' . $id,
            'type' => 'required|in:route,action,ui_element,section',
            'description' => 'nullable|string',
        ]);

        try {
            $feature = $this->featureService->updateFeature($id, $validated);
            
            return response()->json([
                'success' => true,
                'message' => __('Feature updated successfully.'),
                'data' => $feature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified feature
     */
    public function destroy(string $id)
    {
        try {
            $this->featureService->deleteFeature($id);
            
            return response()->json([
                'success' => true,
                'message' => __('Feature deleted successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
