<?php

namespace App\Models;

use App\Models\Concerns\UsesIsDeletedFlag;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory, UsesIsDeletedFlag;

    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'department',
        'position',
        'hire_date',
        'status',
        'dept_id',
        
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'status' => 'boolean',
            'is_deleted' => 'boolean',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => $this->first_name.' '.$this->last_name);
    }
}
