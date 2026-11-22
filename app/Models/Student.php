<?php

namespace App\Models;

use App\Models\Concerns\UsesIsDeletedFlag;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory, UsesIsDeletedFlag;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'dept_id',
        'course',
        'year_level',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year_level' => 'integer',
            'status' => 'boolean',
            'is_deleted' => 'boolean',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => $this->first_name.' '.$this->last_name);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}