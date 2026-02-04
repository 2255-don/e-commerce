<?php

namespace Tests\Unit;

use App\Models\Feature;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $permissionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permissionService = app(PermissionService::class);
    }

    public function test_can_create_permission()
    {
        $data = [
            'name' => 'Test Permission',
            'slug' => 'test.permission',
            'description' => 'Test description',
        ];

        $permission = $this->permissionService->createPermission($data);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('test.permission', $permission->slug);
    }

    public function test_auto_generates_slug_if_not_provided()
    {
        $data = [
            'name' => 'Auto Slug Test',
            'description' => 'Test description',
        ];

        $permission = $this->permissionService->createPermission($data);

        $this->assertEquals('auto-slug-test', $permission->slug);
    }

    public function test_can_sync_role_permissions()
    {
        $role = Role::create(['nom' => 'Test Role', 'slug' => 'test-role']);
        $permission1 = Permission::create(['name' => 'Perm 1', 'slug' => 'perm.1']);
        $permission2 = Permission::create(['name' => 'Perm 2', 'slug' => 'perm.2']);

        $this->permissionService->syncRolePermissions(
            $role->id,
            [$permission1->id, $permission2->id]
        );

        $this->assertCount(2, $role->fresh()->permissions);
    }

    public function test_can_generate_default_permissions_for_module()
    {
        $module = Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
        ]);

        $permissions = $this->permissionService->generateDefaultPermissionsForModule($module->id);

        $this->assertCount(4, $permissions); // view, create, edit, delete
        $this->assertEquals('test-module.view', $permissions[0]->slug);
    }

    public function test_user_has_permission_check()
    {
        $user = User::factory()->create();
        $role = Role::create(['nom' => 'Test Role', 'slug' => 'test-role']);
        $permission = Permission::create(['name' => 'Test Perm', 'slug' => 'test.perm']);
        
        $user->roles()->attach($role->id);
        $role->permissions()->attach($permission->id);

        $hasPermission = $this->permissionService->userHasPermission($user, 'test.perm');

        $this->assertTrue($hasPermission);
    }

    public function test_user_can_access_feature_check()
    {
        $user = User::factory()->create();
        $role = Role::create(['nom' => 'Test Role', 'slug' => 'test-role']);
        $permission = Permission::create(['name' => 'Test Perm', 'slug' => 'test.perm']);
        $module = Module::create(['name' => 'Test', 'slug' => 'test']);
        $feature = Feature::create([
            'module_id' => $module->id,
            'name' => 'Test Feature',
            'slug' => 'test.feature',
            'type' => 'route',
        ]);
        
        $user->roles()->attach($role->id);
        $role->permissions()->attach($permission->id);
        $permission->features()->attach($feature->id);

        $canAccess = $this->permissionService->userCanAccessFeature($user, 'test.feature');

        $this->assertTrue($canAccess);
    }
}
