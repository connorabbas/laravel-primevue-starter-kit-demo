<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::where(['name' => 'Super Admin'])->delete();
        Role::where(['name' => 'Admin'])->delete();
    }
};
