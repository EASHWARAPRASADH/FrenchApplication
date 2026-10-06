@extends('layouts.admin')

@section('title', 'Users Management - Admin Panel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Users Management</h1>
            <p class="text-muted">Manage all platform users</p>
        </div>
        <div>
            <button class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Add New User
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i>Please fix the following issues:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Users ({{ $users->total() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ $user->initials }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'teacher' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->diffForHumans() }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($user->role === 'student')
                                            <a href="{{ route('admin.students.analytics', $user) }}" class="btn btn-outline-info"
                                                title="Analytics">
                                                <i class="bi bi-bar-chart"></i>
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-primary" title="View Profile & Details"
                                            onclick="openUserDetailsModal({{ $user->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" title="Reset Password / Generate Link"
                                            data-bs-toggle="modal" data-bs-target="#userPasswordModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-reset-url="{{ route('admin.users.reset-password', $user) }}"
                                            data-generate-url="{{ route('admin.users.generate-reset-link', $user) }}">
                                            <i class="bi bi-key-fill"></i>
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-outline-danger" title="Delete User"
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-user-role="{{ ucfirst($user->role) }}"
                                                data-has-courses="{{ $user->courses()->exists() ? '1' : '0' }}"
                                                data-courses-count="{{ $user->courses()->count() }}"
                                                data-delete-url="{{ route('admin.users.destroy', $user) }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        No users found
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Delete User Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteUserForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteUserModalLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete User Account
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="teacherWarning" class="alert alert-warning d-none">
                            <strong><i class="bi bi-shield-lock me-1"></i>Cannot delete instructor:</strong>
                            This user is assigned as instructor to <span id="teacherCoursesCount">0</span> course(s). Reassign these courses first before deleting.
                        </div>

                        <p>Are you sure you want to permanently delete the following user?</p>
                        <div class="card bg-light border p-3 mb-3">
                            <div><strong>Name:</strong> <span id="modalUserName">-</span></div>
                            <div><strong>Email:</strong> <span id="modalUserEmail">-</span></div>
                            <div><strong>Role:</strong> <span id="modalUserRole" class="badge bg-secondary">-</span></div>
                        </div>

                        <div class="alert alert-danger py-2 mb-0">
                            <small>
                                <i class="bi bi-trash me-1"></i><strong>Warning:</strong> This action is permanent and cannot be undone. All test attempts, progress history, permissions, and enrollments for this user will be removed.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="confirmDeleteBtn" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Permanently Delete User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Management & 1-Click Link Modal -->
    <div class="modal fade" id="userPasswordModal" tabindex="-1" aria-labelledby="userPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom">
                    <h5 class="modal-title d-flex align-items-center" id="userPasswordModalLabel">
                        <i class="bi bi-shield-lock-fill text-warning me-2 fs-4"></i>
                        <span>Manage Password: <strong id="pwdModalUserName">-</strong></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        User Email: <code id="pwdModalUserEmail" class="fw-semibold text-dark">-</code>
                    </p>

                    <!-- Nav tabs -->
                    <ul class="nav nav-pills nav-fill mb-3" id="pwdTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="direct-pwd-tab" data-bs-toggle="tab" data-bs-target="#direct-pwd" type="button" role="tab">
                                <i class="bi bi-pencil-square me-1"></i>Direct Set Password
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="link-pwd-tab" data-bs-toggle="tab" data-bs-target="#link-pwd" type="button" role="tab">
                                <i class="bi bi-link-45deg me-1"></i>1-Click Reset Link
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content" id="pwdTabContent">
                        <!-- Direct Set Password Tab -->
                        <div class="tab-pane fade show active" id="direct-pwd" role="tabpanel">
                            <form id="directPasswordForm" method="POST" action="">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="newPasswordInput" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPasswordInput', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="newPasswordConfirmInput" class="form-control" placeholder="Confirm new password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPasswordConfirmInput', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="alert alert-light border small py-2 text-muted mb-3">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Immediately updates the user password in the database. The user can log in right away without needing email verification.
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check2-circle me-1"></i>Save New Password
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- 1-Click Link Tab -->
                        <div class="tab-pane fade" id="link-pwd" role="tabpanel">
                            <div class="alert alert-info small py-2 mb-3">
                                <i class="bi bi-shield-check me-1"></i>
                                Generates a secure, temporary reset link. You can send this link directly to the student via WhatsApp or message to let them choose their own password.
                            </div>
                            <div class="d-grid mb-3">
                                <button type="button" id="btnGenerateResetLink" class="btn btn-outline-primary">
                                    <i class="bi bi-magic me-1"></i>Generate Reset Link Now
                                </button>
                            </div>
                            <div id="resetLinkResultContainer" class="d-none">
                                <label class="form-label small fw-semibold">Direct Reset URL</label>
                                <div class="input-group mb-2">
                                    <input type="text" id="generatedResetUrlInput" class="form-control font-monospace small" readonly>
                                    <button class="btn btn-success" type="button" id="btnCopyResetUrl">
                                        <i class="bi bi-clipboard me-1"></i>Copy
                                    </button>
                                </div>
                                <div id="copySuccessFeedback" class="text-success small fw-semibold d-none">
                                    <i class="bi bi-check-circle me-1"></i>Link copied to clipboard! You can send it directly to the user.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View User Details & Performance Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-weight: 700; font-size: 1.2rem;">
                            <span id="vUserAvatar">U</span>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="viewUserModalLabel">
                                <span id="vUserModalName">User Details</span>
                            </h5>
                            <div class="small text-muted" id="vUserEmail">-</div>
                        </div>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <span id="vUserRoleBadge" class="badge bg-secondary">-</span>
                        <span id="vUserStatusBadge" class="badge bg-success">-</span>
                        <span id="vUserLevelBadge" class="badge bg-info text-dark">-</span>
                        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4">
                    <!-- Loading Spinner -->
                    <div id="vUserLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="text-muted small mt-2">Loading user details & performance...</div>
                    </div>

                    <!-- Content Container -->
                    <div id="vUserContent" class="d-none">
                        <!-- Key Stats Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="card bg-light border-0 p-3 text-center">
                                    <div class="text-muted small">Joined Date</div>
                                    <div class="fw-bold fs-6 text-dark" id="vUserJoined">-</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card bg-light border-0 p-3 text-center">
                                    <div class="text-muted small">Last Activity</div>
                                    <div class="fw-bold fs-6 text-dark" id="vUserLastLogin">-</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card bg-light border-0 p-3 text-center">
                                    <div class="text-muted small">Attendance Rate</div>
                                    <div class="fw-bold fs-6 text-primary" id="vAttendanceRate">0%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs mb-3" id="vUserTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="v-courses-tab" data-bs-toggle="tab" data-bs-target="#v-courses-pane" type="button" role="tab">
                                    <i class="bi bi-book me-1"></i>Enrolled Courses (<span id="vCoursesCount">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="v-tests-tab" data-bs-toggle="tab" data-bs-target="#v-tests-pane" type="button" role="tab">
                                    <i class="bi bi-patch-check me-1"></i>Test History (<span id="vTestsCount">0</span>)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="v-att-tab" data-bs-toggle="tab" data-bs-target="#v-att-pane" type="button" role="tab">
                                    <i class="bi bi-calendar-check me-1"></i>Attendance
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="vUserTabsContent">
                            <!-- Courses Pane -->
                            <div class="tab-pane fade show active" id="v-courses-pane" role="tabpanel">
                                <div id="vCoursesList"></div>
                            </div>

                            <!-- Tests Pane -->
                            <div class="tab-pane fade" id="v-tests-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test Title</th>
                                                <th>Course</th>
                                                <th>Score</th>
                                                <th>Result</th>
                                                <th>Submitted</th>
                                            </tr>
                                        </thead>
                                        <tbody id="vSubmissionsList"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Attendance Pane -->
                            <div class="tab-pane fade" id="v-att-pane" role="tabpanel">
                                <div class="card border-0 bg-light p-3">
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="text-muted small">Total Sessions</div>
                                            <div class="fs-4 fw-bold" id="vAttTotal">0</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Present Sessions</div>
                                            <div class="fs-4 fw-bold text-success" id="vAttPresent">0</div>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div id="vAttRateBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <div class="text-muted small text-center mt-2" id="vAttRateText">0% attendance</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delete User Modal
        const deleteModal = document.getElementById('deleteUserModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userName = button.getAttribute('data-user-name');
                const userEmail = button.getAttribute('data-user-email');
                const userRole = button.getAttribute('data-user-role');
                const hasCourses = button.getAttribute('data-has-courses') === '1';
                const coursesCount = button.getAttribute('data-courses-count') || '0';
                const deleteUrl = button.getAttribute('data-delete-url');

                document.getElementById('modalUserName').textContent = userName;
                document.getElementById('modalUserEmail').textContent = userEmail;
                document.getElementById('modalUserRole').textContent = userRole;
                document.getElementById('deleteUserForm').action = deleteUrl;

                const teacherWarning = document.getElementById('teacherWarning');
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

                if (hasCourses) {
                    teacherWarning.classList.remove('d-none');
                    document.getElementById('teacherCoursesCount').textContent = coursesCount;
                    confirmDeleteBtn.disabled = true;
                    confirmDeleteBtn.classList.add('disabled');
                } else {
                    teacherWarning.classList.add('d-none');
                    confirmDeleteBtn.disabled = false;
                    confirmDeleteBtn.classList.remove('disabled');
                }
            });
        }

        // Password Modal
        const pwdModal = document.getElementById('userPasswordModal');
        let currentGenerateUrl = '';

        if (pwdModal) {
            pwdModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userName = button.getAttribute('data-user-name');
                const userEmail = button.getAttribute('data-user-email');
                const resetUrl = button.getAttribute('data-reset-url');
                currentGenerateUrl = button.getAttribute('data-generate-url');

                document.getElementById('pwdModalUserName').textContent = userName;
                document.getElementById('pwdModalUserEmail').textContent = userEmail;
                document.getElementById('directPasswordForm').action = resetUrl;
                document.getElementById('newPasswordInput').value = '';
                document.getElementById('newPasswordConfirmInput').value = '';

                // Reset link state
                document.getElementById('resetLinkResultContainer').classList.add('d-none');
                document.getElementById('copySuccessFeedback').classList.add('d-none');
                document.getElementById('generatedResetUrlInput').value = '';

                // Activate first tab
                const firstTabBtn = document.getElementById('direct-pwd-tab');
                if (firstTabBtn && window.bootstrap) {
                    const tab = new bootstrap.Tab(firstTabBtn);
                    tab.show();
                }
            });

            const btnGen = document.getElementById('btnGenerateResetLink');
            if (btnGen) {
                btnGen.addEventListener('click', function() {
                    if (!currentGenerateUrl) return;
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                        document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

                    fetch(currentGenerateUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-magic me-1"></i>Regenerate Link';
                        if (data.success && data.reset_url) {
                            document.getElementById('generatedResetUrlInput').value = data.reset_url;
                            document.getElementById('resetLinkResultContainer').classList.remove('d-none');
                        } else {
                            alert(data.message || 'Failed to generate reset link.');
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-magic me-1"></i>Generate Reset Link Now';
                        alert('Error generating reset link: ' + err.message);
                    });
                });
            }

            const btnCopy = document.getElementById('btnCopyResetUrl');
            if (btnCopy) {
                btnCopy.addEventListener('click', function() {
                    const input = document.getElementById('generatedResetUrlInput');
                    if (!input.value) return;
                    input.select();
                    navigator.clipboard.writeText(input.value).then(() => {
                        const feedback = document.getElementById('copySuccessFeedback');
                        feedback.classList.remove('d-none');
                        setTimeout(() => feedback.classList.add('d-none'), 3000);
                    }).catch(() => {
                        // Fallback for older browsers
                        document.execCommand('copy');
                        const feedback = document.getElementById('copySuccessFeedback');
                        feedback.classList.remove('d-none');
                        setTimeout(() => feedback.classList.add('d-none'), 3000);
                    });
                });
            }
        }
    });

    window.openUserDetailsModal = function(userId) {
        const modalEl = document.getElementById('viewUserModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        document.getElementById('vUserLoading').classList.remove('d-none');
        document.getElementById('vUserContent').classList.add('d-none');
        document.getElementById('vUserModalName').textContent = 'Loading...';

        fetch(`/admin/users/${userId}/details`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('vUserLoading').classList.add('d-none');
            document.getElementById('vUserContent').classList.remove('d-none');
            if (!data.success) {
                alert('Failed to load user details.');
                return;
            }

            const u = data.user;
            document.getElementById('vUserModalName').textContent = u.name;
            document.getElementById('vUserAvatar').textContent = (u.name || 'U').charAt(0).toUpperCase();
            document.getElementById('vUserEmail').textContent = u.email;
            document.getElementById('vUserJoined').textContent = u.joined_at;
            document.getElementById('vUserLastLogin').textContent = u.last_login;
            document.getElementById('vUserRoleBadge').textContent = u.role;
            document.getElementById('vUserStatusBadge').textContent = u.status;
            document.getElementById('vUserLevelBadge').textContent = u.language_level;

            // Courses
            document.getElementById('vCoursesCount').textContent = data.courses.length;
            const coursesList = document.getElementById('vCoursesList');
            coursesList.innerHTML = '';
            if (data.courses.length === 0) {
                coursesList.innerHTML = '<div class="text-muted p-4 text-center">No enrolled courses.</div>';
            } else {
                data.courses.forEach(c => {
                    coursesList.innerHTML += `
                        <div class="card mb-2 border shadow-none bg-light">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">${c.title} <span class="badge bg-white text-dark border ms-1">${c.level}</span></h6>
                                    <div class="small text-muted">Enrolled: ${c.enrolled_at} &bull; Status: <span class="text-success fw-semibold">${c.status}</span></div>
                                </div>
                                <div class="text-end" style="min-width: 140px;">
                                    <div class="fw-bold small mb-1">${c.progress_percentage}% completed</div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: ${c.progress_percentage}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });
            }

            // Submissions
            document.getElementById('vTestsCount').textContent = data.submissions.length;
            const subsList = document.getElementById('vSubmissionsList');
            subsList.innerHTML = '';
            if (data.submissions.length === 0) {
                subsList.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No test submissions yet.</td></tr>';
            } else {
                data.submissions.forEach(s => {
                    const badge = s.passed ? '<span class="badge bg-success">Passed</span>' : (s.status === 'pending' ? '<span class="badge bg-warning text-dark">Pending</span>' : '<span class="badge bg-danger">Failed</span>');
                    subsList.innerHTML += `
                        <tr>
                            <td class="fw-semibold">${s.test_title}</td>
                            <td class="text-muted small">${s.course_title}</td>
                            <td><strong>${s.score !== null ? s.score + '%' : 'N/A'}</strong></td>
                            <td>${badge}</td>
                            <td class="text-muted small">${s.submitted_at}</td>
                        </tr>`;
                });
            }

            // Attendance
            const att = data.attendance;
            document.getElementById('vAttendanceRate').textContent = att.attendance_rate + '%';
            document.getElementById('vAttTotal').textContent = att.total_sessions;
            document.getElementById('vAttPresent').textContent = att.present_sessions;
            document.getElementById('vAttRateBar').style.width = att.attendance_rate + '%';
            document.getElementById('vAttRateText').textContent = `${att.present_sessions} / ${att.total_sessions} sessions (${att.attendance_rate}%)`;

            // Reset tab to courses
            const firstTab = document.getElementById('v-courses-tab');
            if (firstTab && window.bootstrap) {
                new bootstrap.Tab(firstTab).show();
            }
        })
        .catch(err => {
            document.getElementById('vUserLoading').classList.add('d-none');
            alert('Error loading user details: ' + err.message);
        });
    };

    window.togglePasswordVisibility = function(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
        } else {
            input.type = 'password';
            btn.innerHTML = '<i class="bi bi-eye"></i>';
        }
    };
    </script>
@endsection