<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Admin::create([
            'name' => 'Connor Abbas',
            'email' => 'abbasconnor@gmail.com',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
        ]);
    }
};
