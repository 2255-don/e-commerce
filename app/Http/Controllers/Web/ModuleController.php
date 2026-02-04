<?php

namespace App\Http\Controllers\Web;

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
        $modules = $this->moduleService->getAllModules();
        
        return view('pages.admin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new module
     */
    public function create()
    {
        return view('pages.admin.modules.create');
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
            $this->moduleService->createModule($validated);
            
            return redirect()->route('admin.modules.index')
                ->with('success', __('Module created successfully.'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing a module
     */
    public function edit(string $id)
    {
        $module = $this->moduleService->getModuleWithFeatures($id);
        
        return view('pages.admin.modules.edit', compact('module'));
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
            $this->moduleService->updateModule($id, $validated);
            
            return redirect()->route('admin.modules.index')
                ->with('success', __('Module updated successfully.'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified module
     */
    public function destroy(string $id)
    {
        try {
            $this->moduleService->deleteModule($id);
            
            return redirect()->route('admin.modules.index')
                ->with('success', __('Module deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
