<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (app()->isProduction()) {
            $user = User::where('email', 'abbasconnor@gmail.com')->first();
            if ($user) {
                $user->assignRole('Admin');
            }
        }
    }
};
