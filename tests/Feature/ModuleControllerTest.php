<?php

namespace Tests\Feature;

use App\Models\Module;
use Modules\Identity\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user with super-admin role
        $this->user = User::factory()->create();
    }

    public function test_can_view_modules_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.modules.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.admin.modules.index');
    }

    public function test_can_create_module()
    {
        $moduleData = [
            'name' => 'Test Module',
            'slug' => 'test-module',
            'icon' => 'bx-test',
            'color' => '#B8860B',
            'order' => 1,
            'is_core' => false,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.modules.store'), $moduleData);

        $response->assertRedirect(route('admin.modules.index'));
        $this->assertDatabaseHas('modules', ['slug' => 'test-module']);
    }

    public function test_can_update_module()
    {
        $module = Module::create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
            'icon' => 'bx-original',
            'color' => '#000000',
            'order' => 1,
        ]);

        $updatedData = [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
            'icon' => 'bx-updated',
            'color' => '#FFFFFF',
            'order' => 2,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.modules.update', $module->id), $updatedData);

        $response->assertRedirect(route('admin.modules.index'));
        $this->assertDatabaseHas('modules', ['slug' => 'updated-slug']);
    }

    public function test_cannot_delete_core_module()
    {
        $coreModule = Module::create([
            'name' => 'Core Module',
            'slug' => 'core-module',
            'is_core' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('admin.modules.destroy', $coreModule->id));

        $this->assertDatabaseHas('modules', ['id' => $coreModule->id]);
    }

    public function test_can_delete_non_core_module()
    {
        $module = Module::create([
            'name' => 'Custom Module',
            'slug' => 'custom-module',
            'is_core' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('admin.modules.destroy', $module->id));

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }
}
