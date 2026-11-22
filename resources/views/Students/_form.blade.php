@csrf

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="student_id" class="form-label">Student ID</label>
        <input type="text" name="student_id" id="student_id" class="form-control"
               value="{{ old('student_id', $student->student_id ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control"
               value="{{ old('first_name', $student->first_name ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control"
               value="{{ old('last_name', $student->last_name ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control"
               value="{{ old('email', $student->email ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control"
               value="{{ old('phone', $student->phone ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="dept_id" class="form-label">College / Department</label>
        <select name="dept_id" id="dept_id" class="form-select">
            <option value="">-- Select college --</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}"
                    @selected((string) old('dept_id', $student->dept_id ?? '') === (string) $department->id)>
                    {{ $department->college_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-5 mb-3">
        <label for="course" class="form-label">Degree Program / Course</label>
        <input type="text" name="course" id="course" class="form-control"
               value="{{ old('course', $student->course ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="year_level" class="form-label">Year Level</label>
        <input type="number" name="year_level" id="year_level" min="1" max="4" class="form-control"
               value="{{ old('year_level', $student->year_level ?? '') }}">
    </div>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="status" id="status" value="1" class="form-check-input"
           @checked(old('status', $student->status ?? true))>
    <label for="status" class="form-check-label">Active</label>
</div>