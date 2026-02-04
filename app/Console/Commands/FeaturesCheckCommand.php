<?php

namespace App\Console\Commands;

use App\Services\FeatureService;
use Illuminate\Console\Command;

class FeaturesCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:check {--fix : Automatically remove orphaned features}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for orphaned and missing features';

    protected $featureService;

    public function __construct(FeatureService $featureService)
    {
        parent::__construct();
        $this->featureService = $featureService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking features consistency...');
        $this->newLine();

        $result = $this->featureService->checkOrphanedFeatures();

        // Display orphaned features (in DB but not in code)
        if (count($result['orphaned']) > 0) {
            $this->warn('⚠️  Orphaned Features (in DB but not in code):');
            $this->table(
                ['Slug'],
                collect($result['orphaned'])->map(fn($slug) => [$slug])->toArray()
            );

            if ($this->option('fix')) {
                $this->warn('Removing orphaned features...');
                \App\Models\Feature::whereIn('slug', $result['orphaned'])->delete();
                $this->info('✓ Orphaned features removed.');
            } else {
                $this->comment('Run with --fix to automatically remove these features.');
            }
            $this->newLine();
        } else {
            $this->info('✓ No orphaned features found.');
            $this->newLine();
        }

        // Display missing features (in code but not in DB)
        if (count($result['missing']) > 0) {
            $this->warn('⚠️  Missing Features (in code but not in DB):');
            $this->table(
                ['Slug'],
                collect($result['missing'])->map(fn($slug) => [$slug])->toArray()
            );
            $this->comment('Run "php artisan features:scan" to register these features.');
            $this->newLine();
        } else {
            $this->info('✓ No missing features found.');
            $this->newLine();
        }

        // Summary
        $this->info('📊 Summary:');
        $this->info("   Orphaned: " . count($result['orphaned']));
        $this->info("   Missing: " . count($result['missing']));

        if (count($result['orphaned']) === 0 && count($result['missing']) === 0) {
            $this->info("\n✅ All features are in sync!");
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
