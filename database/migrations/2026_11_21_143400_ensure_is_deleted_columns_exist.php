<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'is_deleted')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_deleted')->default(0)->index();
            });
        }

        if (Schema::hasTable('employees') && ! Schema::hasColumn('employees', 'is_deleted')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->boolean('is_deleted')->default(0)->index();
            });
        }
    }

    public function down(): void
    {
        // Preserve existing soft-delete data and do not drop the columns.
    }
};
