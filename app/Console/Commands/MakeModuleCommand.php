<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name : The name of the module}
                            {--icon=bx-cube : Boxicons icon class}
                            {--color=#B8860B : Hex color code}
                            {--core : Mark as core module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new module with default configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $slug = Str::slug($name);
        
        // Check if module already exists
        if (Module::where('slug', $slug)->exists()) {
            $this->error("Module '{$slug}' already exists!");
            return Command::FAILURE;
        }

        $this->info("🔧 Creating module: {$name}");
        $this->newLine();

        // Create the module
        $module = Module::create([
            'slug' => $slug,
            'name' => $name,
            'icon' => $this->option('icon'),
            'color' => $this->option('color'),
            'order' => Module::max('order') + 1,
            'is_core' => $this->option('core'),
        ]);

        $this->info("✓ Module created successfully!");
        $this->table(
            ['Property', 'Value'],
            [
                ['Name', $module->name],
                ['Slug', $module->slug],
                ['Icon', $module->icon],
                ['Color', $module->color],
                ['Order', $module->order],
                ['Core', $module->is_core ? 'Yes' : 'No'],
            ]
        );

        $this->newLine();
        
        // Ask if user wants to generate default permissions
        if ($this->confirm('Generate default CRUD permissions for this module?', true)) {
            $this->call('permissions:generate', ['module' => $slug]);
        }

        $this->newLine();
        $this->info('📝 Next steps:');
        $this->comment('  1. Run "php artisan features:scan" to detect features for this module');
        $this->comment('  2. Assign permissions to roles via the admin interface');
        $this->comment('  3. Use feature components in your Blade views');

        return Command::SUCCESS;
    }
}
