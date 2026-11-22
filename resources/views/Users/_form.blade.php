@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control"
               value="{{ old('name', $user->name ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control"
               value="{{ old('email', $user->email ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">
            Password
            @isset($user)
                <small class="text-muted">(leave blank to keep current)</small>
            @endisset
        </label>
        <input type="password" name="password" id="password" class="form-control"
               autocomplete="new-password" @empty($user) required @endempty>
    </div>

    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="form-control" autocomplete="new-password">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="role_id" class="form-label">Role</label>
        <select name="role_id" id="role_id" class="form-select" required>
            <option value="">-- Select role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}"
                    @selected((int) old('role_id', $user->role_id ?? 0) === $role->id)>
                    {{ $role->label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input type="checkbox" name="status" id="status" value="1" class="form-check-input"
                   @checked(old('status', $user->status ?? true))>
            <label for="status" class="form-check-label">Active (can log in)</label>
        </div>
    </div>
</div>