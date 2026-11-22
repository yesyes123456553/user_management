<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('label');
                $table->timestamps();
            });
        }

        $now = now();

        foreach ([
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'super_user' => 'Super User',
            'user' => 'User',
        ] as $name => $label) {
            DB::table('roles')->updateOrInsert(
                ['name' => $name],
                ['label' => $label, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        if (! Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            });
        }

        $superAdminId = DB::table('roles')->where('name', 'super_admin')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');

        DB::table('users')->whereNull('role_id')->update(['role_id' => $userRoleId]);

        if (! DB::table('users')->where('role_id', $superAdminId)->exists()) {
            $administratorId = DB::table('users')->where('email', 'admin@example.com')->value('id')
                ?? DB::table('users')->orderBy('id')->value('id');

            if ($administratorId !== null) {
                DB::table('users')->where('id', $administratorId)->update(['role_id' => $superAdminId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('role_id');
            });
        }

        Schema::dropIfExists('roles');
    }
};