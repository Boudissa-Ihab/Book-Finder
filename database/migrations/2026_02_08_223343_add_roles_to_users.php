<?php

use App\Enums\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::firstOrCreate(['name' => Roles::ADMIN]);
        Role::firstOrCreate(['name' => Roles::USER]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::delete();
    }
};
