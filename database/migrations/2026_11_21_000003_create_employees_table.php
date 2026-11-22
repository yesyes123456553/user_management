<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('employee_code')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('department')->nullable();
                $table->string('position')->nullable();
                $table->date('hire_date')->nullable();
                $table->boolean('status')->default(1);
                $table->boolean('is_deleted')->default(0)->index();
                $table->timestamps();
            });

            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'employee_code')) {
                $table->string('employee_code')->nullable();
            }
            if (! Schema::hasColumn('employees', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (! Schema::hasColumn('employees', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (! Schema::hasColumn('employees', 'email')) {
                $table->string('email')->nullable();
            }
            if (! Schema::hasColumn('employees', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (! Schema::hasColumn('employees', 'department')) {
                $table->string('department')->nullable();
            }
            if (! Schema::hasColumn('employees', 'position')) {
                $table->string('position')->nullable();
            }
            if (! Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }
            if (! Schema::hasColumn('employees', 'status')) {
                $table->boolean('status')->default(1);
            }
            if (! Schema::hasColumn('employees', 'is_deleted')) {
                $table->boolean('is_deleted')->default(0)->index();
            }
            if (! Schema::hasColumn('employees', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (! Schema::hasColumn('employees', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        if (Schema::hasColumn('employees', 'name')) {
            $rows = DB::table('employees')
                ->select(['id', 'name'])
                ->get();

            foreach ($rows as $row) {
                $name = trim((string) $row->name);
                $parts = preg_split('/\s+/', $name, 2) ?: [];
                $firstName = $parts[0] ?? '';
                $lastName = $parts[1] ?? '';

                DB::table('employees')
                    ->where('id', $row->id)
                    ->update([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]);
            }
        }

        if (Schema::hasColumn('employees', 'employee_code')) {
            $rows = DB::table('employees')
                ->select(['id', 'employee_code'])
                ->where(function ($query) {
                    $query->whereNull('employee_code')
                        ->orWhere('employee_code', '');
                })
                ->get();

            foreach ($rows as $row) {
                DB::table('employees')
                    ->where('id', $row->id)
                    ->update([
                        'employee_code' => sprintf('EMP-%06d', $row->id),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Do not drop or otherwise remove employee records during rollback.
    }
};
