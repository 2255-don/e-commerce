<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\FeatureService;
use App\Services\ModuleService;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    protected $featureService;
    protected $moduleService;

    public function __construct(FeatureService $featureService, ModuleService $moduleService)
    {
        $this->featureService = $featureService;
        $this->moduleService = $moduleService;
    }

    /**
     * Display a listing of features
     */
    public function index(Request $request)
    {
        $moduleId = $request->get('module');
        $type = $request->get('type');

        if ($moduleId) {
            $features = $this->featureService->getFeaturesByModule($moduleId);
        } elseif ($type) {
            $features = $this->featureService->getFeaturesByType($type);
        } else {
            $features = $this->featureService->getAllFeatures();
        }

        $modules = $this->moduleService->getAllModules();
        
        return view('pages.admin.features.index', compact('features', 'modules'));
    }

    /**
     * Show the form for creating a new feature
     */
    public function create()
    {
        $modules = $this->moduleService->getAllModules();
        
        return view('pages.admin.features.create', compact('modules'));
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
            $this->featureService->createFeature($validated);
            
            return redirect()->route('admin.features.index')
                ->with('success', __('Feature created successfully.'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing a feature
     */
    public function edit(string $id)
    {
        $feature = $this->featureService->getFeature($id);
        $modules = $this->moduleService->getAllModules();
        
        return view('pages.admin.features.edit', compact('feature', 'modules'));
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
            $this->featureService->updateFeature($id, $validated);
            
            return redirect()->route('admin.features.index')
                ->with('success', __('Feature updated successfully.'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified feature
     */
    public function destroy(string $id)
    {
        try {
            $this->featureService->deleteFeature($id);
            
            return redirect()->route('admin.features.index')
                ->with('success', __('Feature deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
