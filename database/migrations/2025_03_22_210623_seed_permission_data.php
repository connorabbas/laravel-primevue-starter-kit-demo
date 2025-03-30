<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class () extends Migration {
    protected array $defaultAdminPermissions = [
        'view users list',
        'view single user',
        'create user',
        'update user',
        'delete user',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->defaultAdminPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($this->defaultAdminPermissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($adminRole = Role::where('name', 'Admin')->first()) {
            $adminRole->delete();
        }

        foreach ($this->defaultAdminPermissions as $permissionName) {
            if ($permission = Permission::where('name', $permissionName)->first()) {
                $permission->delete();
            }
        }
    }
};
