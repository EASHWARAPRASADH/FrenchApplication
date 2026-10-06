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
                                        <button class="btn btn-outline-primary" title="View"
                                            onclick="alert('View user details - feature coming soon')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Edit"
                                            onclick="alert('Edit user - feature coming soon')">
                                            <i class="bi bi-pencil"></i>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteUserModal');
        if (!deleteModal) return;

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
    });
    </script>
@endsection