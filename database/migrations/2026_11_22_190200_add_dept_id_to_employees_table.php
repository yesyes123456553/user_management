<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('dept_id')->nullable()->after('phone')->constrained('departments')->nullOnDelete();
        });

        DB::table('employees')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department')
            ->each(function (string $name): void {
                $departmentId = DB::table('departments')->where('college_name', $name)->value('id')
                    ?? DB::table('departments')->insertGetId([
                        'college_name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                DB::table('employees')->where('department', $name)->update(['dept_id' => $departmentId]);
            });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dept_id');
        });
    }
};