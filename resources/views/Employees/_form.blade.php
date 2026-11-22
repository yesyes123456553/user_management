@csrf

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="employee_code" class="form-label">Employee Code</label>
        <input type="text" name="employee_code" id="employee_code" class="form-control"
               value="{{ old('employee_code', $employee->employee_code ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control"
               value="{{ old('first_name', $employee->first_name ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control"
               value="{{ old('last_name', $employee->last_name ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control"
               value="{{ old('email', $employee->email ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control"
               value="{{ old('phone', $employee->phone ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="department" class="form-label">Department</label>
        <input type="text" name="department" id="department" class="form-control"
               value="{{ old('department', $employee->department ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="position" class="form-label">Position</label>
        <input type="text" name="position" id="position" class="form-control"
               value="{{ old('position', $employee->position ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="hire_date" class="form-label">Hire Date</label>
        <input type="date" name="hire_date" id="hire_date" class="form-control"
               value="{{ old('hire_date', isset($employee) ? $employee->hire_date?->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="status" id="status" value="1" class="form-check-input"
           @checked(old('status', $employee->status ?? true))>
    <label for="status" class="form-check-label">Active</label>
</div>