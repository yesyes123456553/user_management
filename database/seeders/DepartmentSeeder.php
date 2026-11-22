<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $colleges = [
            'College of Agriculture and Related Sciences (CARS)' => 'Institute of Agriculture and Related Sciences (IARS)',
            'College of Business Education and Governance (CBEG)' => 'Institute of Business Education and Governance (IBEG)',
            'College of Engineering and Technology (CET)' => 'Institute of Computing, Engineering and Technology (ICET)',
            'College of Information and Digital Sciences (CIDS)' => null,
            'College of Mathematics, Arts, and Sciences (CMAS)' => 'Institute of Mathematics, Arts, and Sciences (IMAS)',
            'College of Teacher Education (CTED)' => 'Institute of Teacher Education (ITED)',
        ];

        foreach ($colleges as $collegeName => $description) {
            Department::updateOrCreate(
                ['college_name' => $collegeName],
                ['description' => $description],
            );
        }

        Department::whereNotIn('college_name', array_keys($colleges))->delete();
    }
}