<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Services\PermissionService;
use Illuminate\Console\Command;

class PermissionsGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:generate 
                            {module? : Module slug to generate permissions for}
                            {--all : Generate permissions for all modules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate default CRUD permissions for a module or all modules';

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();
        $this->permissionService = $permissionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleSlug = $this->argument('module');
        $all = $this->option('all');

        if (!$moduleSlug && !$all) {
            $this->error('Please specify a module slug or use --all flag.');
            return Command::FAILURE;
        }

        $modules = [];

        if ($all) {
            $modules = Module::all();
            $this->info('🔧 Generating permissions for all modules...');
        } else {
            $module = Module::where('slug', $moduleSlug)->first();
            
            if (!$module) {
                $this->error("Module '{$moduleSlug}' not found.");
                return Command::FAILURE;
            }
            
            $modules = [$module];
            $this->info("🔧 Generating permissions for module: {$module->name}");
        }

        $this->newLine();
        $totalGenerated = 0;

        foreach ($modules as $module) {
            $this->comment("Processing: {$module->name}");
            
            $permissions = $this->permissionService->generateDefaultPermissionsForModule($module->id);
            
            foreach ($permissions as $permission) {
                $this->line("  ✓ {$permission->name} ({$permission->slug})");
            }
            
            $totalGenerated += count($permissions);
            $this->newLine();
        }

        $this->info("✅ Successfully generated {$totalGenerated} permissions!");
        
        return Command::SUCCESS;
    }
}
