<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('migrations ran, woot');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
