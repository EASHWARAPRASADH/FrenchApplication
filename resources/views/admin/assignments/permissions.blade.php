@extends('layouts.admin')

@section('title', 'Permissions: ' . $course->title . ' / ' . $student->name)

@section('content')
<div class="container py-3">
    {{-- Header & Breadcrumbs --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Content Permissions</h3>
            <div class="text-muted small mt-1">
                Course: <strong>{{ $course->title }}</strong> &bull; Student: <span class="badge bg-dark fs-6">{{ $student->name }}</span> ({{ $student->email }})
            </div>
        </div>
        <a href="{{ route('admin.assignments.course.show', $course) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Assignments
        </a>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Alert Container for AJAX Responses --}}
    <div id="ajaxAlertContainer"></div>

    @php
        // Prepare Section & Sets JSON for Quick Allocator dropdown
        $sectionsJsonData = [];
        foreach ($topFolders as $tf) {
            $sets = [];
            foreach ($tf->subfolders as $sub) {
                $isUnlocked = optional($existing->get('folder:' . $sub->id))->has_access ? true : false;
                $sets[] = [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'is_unlocked' => $isUnlocked,
                    'tests_count' => $sub->tests->count(),
                ];
            }
            if (count($sets) > 0) {
                $sectionsJsonData[] = [
                    'id' => $tf->id,
                    'name' => $tf->name,
                    'sets' => $sets,
                ];
            }
        }
    @endphp

    {{-- QUICK SET ALLOCATOR & SEQUENTIAL PROGRESSION CARD --}}
    <div class="card border-primary shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-lightning-charge-fill fs-5 me-2 text-warning"></i>
                <div>
                    <strong class="fs-6">Quick Set Allocator &amp; Sequential Progression</strong>
                    <div class="small text-white-50">Assign one set at a time or unlock sets sequentially after live sessions.</div>
                </div>
            </div>
            <span class="badge bg-light text-primary">Smart Tool</span>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-secondary">
                        <i class="bi bi-folder2-open me-1"></i>1. Select Section / Category
                    </label>
                    <select id="quickSectionSelect" class="form-select form-select-sm shadow-none" onchange="onQuickSectionChange()">
                        @foreach($sectionsJsonData as $sec)
                            <option value="{{ $sec['id'] }}">{{ $sec['name'] }} ({{ count($sec['sets']) }} Sets)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-secondary">
                        <i class="bi bi-collection me-1"></i>2. Select Target Set
                    </label>
                    <select id="quickTargetSelect" class="form-select form-select-sm shadow-none">
                        {{-- Populated dynamically via JS --}}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-secondary">
                        <i class="bi bi-sliders me-1"></i>3. Allocation Action
                    </label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary flex-fill" onclick="executeQuickAction('solo')" title="Grants this set exclusively and locks all other sets in this section.">
                            <i class="bi bi-bullseye me-1"></i>Solo Mode
                        </button>
                        <button type="button" class="btn btn-sm btn-success flex-fill" onclick="executeQuickAction('progressive_next')" title="Unlocks this set without revoking earlier sets.">
                            <i class="bi bi-unlock-fill me-1"></i>Unlock Next
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="executeQuickAction('revoke_section')" title="Locks all sets in this section.">
                            <i class="bi bi-lock-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="text-muted small">
                        <span class="badge bg-primary me-1">Solo Mode</span> Locks all other sibling sets in this section so the student focuses exclusively on this assigned set.
                        <span class="badge bg-success ms-2 me-1">Unlock Next</span> Unlocks this set in addition to previous sets, keeping future sets strictly locked.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRANULAR PERMISSION FORM --}}
    <form id="permissionsForm" method="POST" action="{{ route('admin.assignments.course.permissions.save', ['course' => $course->id, 'student_id' => $student->id]) }}">
        @csrf

        {{-- Filter & Actions Toolbar --}}
        <div class="card mb-3 border-0 bg-transparent">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="treeSearchInput" class="form-control" placeholder="Search folders, sets, or tests in real-time..." onkeyup="filterTree()">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                <div class="col-md-7 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="expandAllSections()">
                        <i class="bi bi-arrows-expand me-1"></i>Expand All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="collapseAllSections()">
                        <i class="bi bi-arrows-collapse me-1"></i>Collapse All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkToggleAll(true)">
                        <i class="bi bi-check-all me-1"></i>Grant All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkToggleAll(false)">
                        <i class="bi bi-x-circle me-1"></i>Revoke All
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save me-1"></i>Save All Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- SECTION 1: ROOT FOLDERS AND THEIR SUB-FOLDERS / SETS --}}
        @foreach($topFolders as $top)
            @php
                $topKey = 'folder:' . $top->id;
                $topHasAccess = optional($existing->get($topKey))->has_access;
                $subCount = $top->subfolders->count();
                $testCount = $top->tests->count();
                foreach($top->subfolders as $s) {
                    $testCount += $s->tests->count();
                }
            @endphp
            <div class="card mb-3 section-tree-card" data-section-id="{{ $top->id }}">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center cursor-pointer" onclick="toggleSectionCollapse({{ $top->id }})">
                    <div class="d-flex align-items-center">
                        <i id="collapse-icon-{{ $top->id }}" class="bi bi-chevron-down me-2 text-muted transition-icon"></i>
                        <i class="bi bi-folder2-open text-primary fs-5 me-2"></i>
                        <div>
                            <strong class="text-dark section-title">{{ $top->name }}</strong>
                            <div class="small text-muted">
                                <span class="badge bg-secondary me-1">{{ $subCount }} Sets</span>
                                <span class="badge bg-warning text-dark">{{ $testCount }} Tests</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                        <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" onclick="grantSectionContent({{ $top->id }})">
                            Grant Section
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" onclick="revokeSectionContent({{ $top->id }})">
                            Revoke Section
                        </button>
                        <div class="form-check form-switch mb-0 ms-2" title="Grant access to this root folder container">
                            <input class="form-check-input perm-cb perm-folder-cb" type="checkbox"
                                name="permissions[folder][{{ $top->id }}]"
                                id="folder_{{ $top->id }}"
                                value="1"
                                data-folder-id="{{ $top->id }}"
                                {{ $topHasAccess ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <div id="section-body-{{ $top->id }}" class="card-body p-0 section-collapse-body">
                    @if($top->subfolders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($top->subfolders as $sub)
                                @php
                                    $subKey = 'folder:' . $sub->id;
                                    $subHasAccess = optional($existing->get($subKey))->has_access;
                                @endphp
                                <div class="list-group-item subfolder-row py-2 px-3" data-subfolder-id="{{ $sub->id }}" data-section-id="{{ $top->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-folder2 text-success me-2"></i>
                                            <span class="subfolder-title fw-semibold">{{ $sub->name }}</span>
                                            @if($subHasAccess)
                                                <span class="badge bg-success ms-2 badge-status-{{ $sub->id }}">Unlocked</span>
                                            @else
                                                <span class="badge bg-secondary ms-2 badge-status-{{ $sub->id }}">Locked</span>
                                            @endif
                                            @if($sub->tests->count() > 0)
                                                <span class="badge bg-light text-dark ms-2">{{ $sub->tests->count() }} test(s)</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" onclick="quickSoloSubfolder({{ $top->id }}, {{ $sub->id }})" title="Assign only this set and lock siblings">
                                                <i class="bi bi-bullseye me-1"></i>Solo
                                            </button>
                                            <div class="form-check form-switch mb-0 ms-2">
                                                <input class="form-check-input perm-cb perm-subfolder-cb sub-of-{{ $top->id }}" type="checkbox"
                                                    name="permissions[folder][{{ $sub->id }}]"
                                                    id="folder_{{ $sub->id }}"
                                                    value="1"
                                                    data-folder-id="{{ $sub->id }}"
                                                    data-section-id="{{ $top->id }}"
                                                    onchange="onSubfolderToggle({{ $sub->id }}, this.checked)"
                                                    {{ $subHasAccess ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tests inside this sub-folder / set --}}
                                    @if($sub->tests->count() > 0)
                                        <div class="mt-2 ps-4 nested-tests-container">
                                            @foreach($sub->tests as $test)
                                                @php
                                                    $testKey = 'test:' . $test->id;
                                                    $testHasAccess = optional($existing->get($testKey))->has_access;
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center py-1 border-top test-row">
                                                    <div class="small">
                                                        <i class="bi bi-clipboard-check text-warning me-2"></i>
                                                        <span class="test-title">{{ $test->title }}</span>
                                                        @if($test->total_questions ?? $test->questions_count)
                                                            <span class="text-muted">({{ $test->total_questions ?? $test->questions_count }} Qs)</span>
                                                        @endif
                                                    </div>
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input perm-cb perm-test-cb test-of-folder-{{ $sub->id }} test-of-sec-{{ $top->id }}" type="checkbox"
                                                            name="permissions[test][{{ $test->id }}]"
                                                            id="test_{{ $test->id }}"
                                                            value="1"
                                                            data-test-id="{{ $test->id }}"
                                                            {{ $testHasAccess ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Lessons inside this sub-folder if any --}}
                                    @if($sub->lessons->count() > 0)
                                        <div class="mt-2 ps-4 nested-lessons-container">
                                            @foreach($sub->lessons as $lesson)
                                                @php
                                                    $lessonKey = 'lesson:' . $lesson->id;
                                                    $lessonHasAccess = optional($existing->get($lessonKey))->has_access;
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center py-1 border-top lesson-row">
                                                    <div class="small">
                                                        <i class="bi bi-play-circle text-primary me-2"></i>
                                                        <span class="lesson-title">{{ $lesson->title }}</span>
                                                    </div>
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input perm-cb perm-lesson-cb lesson-of-folder-{{ $sub->id }} lesson-of-sec-{{ $top->id }}" type="checkbox"
                                                            name="permissions[lesson][{{ $lesson->id }}]"
                                                            id="lesson_{{ $lesson->id }}"
                                                            value="1"
                                                            data-lesson-id="{{ $lesson->id }}"
                                                            {{ $lessonHasAccess ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Direct Tests inside root folder (not in subfolders) --}}
                    @if($top->tests->count() > 0)
                        <div class="p-3 border-top bg-light">
                            <strong class="small text-muted d-block mb-2">Direct Tests in {{ $top->name }}:</strong>
                            @foreach($top->tests as $test)
                                @php
                                    $testKey = 'test:' . $test->id;
                                    $testHasAccess = optional($existing->get($testKey))->has_access;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom test-row">
                                    <div class="small">
                                        <i class="bi bi-clipboard-check text-warning me-2"></i>
                                        <span class="test-title">{{ $test->title }}</span>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input perm-cb perm-test-cb test-of-sec-{{ $top->id }}" type="checkbox"
                                            name="permissions[test][{{ $test->id }}]"
                                            id="test_{{ $test->id }}"
                                            value="1"
                                            data-test-id="{{ $test->id }}"
                                            {{ $testHasAccess ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- SECTION 2: ROOT LEVEL ITEMS (ITEMS NOT IN ANY FOLDER) --}}
        @if($rootLessons->count() > 0 || $rootTests->count() > 0 || $rootFiles->count() > 0)
            <div class="card mb-3 section-tree-card" data-section-id="root-unfoldered">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center cursor-pointer" onclick="toggleSectionCollapse('root-unfoldered')">
                    <div class="d-flex align-items-center">
                        <i id="collapse-icon-root-unfoldered" class="bi bi-chevron-down me-2 text-muted transition-icon"></i>
                        <i class="bi bi-box text-secondary fs-5 me-2"></i>
                        <div>
                            <strong class="text-dark section-title">Root Level Content (Outside Folders)</strong>
                            <div class="small text-muted">
                                {{ $rootLessons->count() }} Lessons &bull; {{ $rootTests->count() }} Tests &bull; {{ $rootFiles->count() }} Files
                            </div>
                        </div>
                    </div>
                </div>
                <div id="section-body-root-unfoldered" class="card-body p-3 section-collapse-body">
                    {{-- Root Lessons --}}
                    @if($rootLessons->count() > 0)
                        <h6 class="text-muted small fw-bold">Root Lessons:</h6>
                        <div class="list-group list-group-flush mb-3">
                            @foreach($rootLessons as $item)
                                @php
                                    $key = 'lesson:' . $item->id;
                                    $hasAccess = optional($existing->get($key))->has_access;
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                    <div><i class="bi bi-play-circle text-primary me-2"></i>{{ $item->title }}</div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input perm-cb" type="checkbox"
                                            name="permissions[lesson][{{ $item->id }}]"
                                            id="root_lesson_{{ $item->id }}"
                                            value="1" {{ $hasAccess ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Root Tests --}}
                    @if($rootTests->count() > 0)
                        <h6 class="text-muted small fw-bold">Root Tests:</h6>
                        <div class="list-group list-group-flush mb-3">
                            @foreach($rootTests as $item)
                                @php
                                    $key = 'test:' . $item->id;
                                    $hasAccess = optional($existing->get($key))->has_access;
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                    <div><i class="bi bi-clipboard-check text-warning me-2"></i>{{ $item->title }}</div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input perm-cb" type="checkbox"
                                            name="permissions[test][{{ $item->id }}]"
                                            id="root_test_{{ $item->id }}"
                                            value="1" {{ $hasAccess ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Root Files --}}
                    @if($rootFiles->count() > 0)
                        <h6 class="text-muted small fw-bold">Root Files:</h6>
                        <div class="list-group list-group-flush">
                            @foreach($rootFiles as $item)
                                @php
                                    $key = 'file:' . $item->id;
                                    $hasAccess = optional($existing->get($key))->has_access;
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                    <div><i class="bi bi-file-earmark text-secondary me-2"></i>{{ $item->filename ?? ('File #' . $item->id) }}</div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input perm-cb" type="checkbox"
                                            name="permissions[file][{{ $item->id }}]"
                                            id="root_file_{{ $item->id }}"
                                            value="1" {{ $hasAccess ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Floating / Sticky Save Footer --}}
        <div class="card mt-4 shadow-sm border-top sticky-bottom-bar bg-white p-3 d-flex flex-row justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>Unchecked content is automatically hidden and protected (deny-by-default).
            </div>
            <div>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i>Save All Permissions
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .transition-icon { transition: transform 0.2s ease-in-out; }
    .btn-xs { font-size: 0.75rem; line-height: 1.4; border-radius: 0.2rem; }
    .sticky-bottom-bar {
        position: sticky;
        bottom: 10px;
        z-index: 100;
        border-radius: 8px;
    }
    .subfolder-row:hover { background-color: #f8fafc; }
</style>
@endpush

@push('scripts')
<script>
    // Embedded sections and sets dataset for client-side dropdown reactivity
    const sectionsDataset = @json($sectionsJsonData);
    const quickAssignUrl = "{{ route('admin.assignments.course.quick-set', ['course' => $course->id, 'student_id' => $student->id]) }}";

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Quick Set dropdown
        onQuickSectionChange();
    });

    // Populate Target Sets dropdown when Category / Section changes
    function onQuickSectionChange() {
        const secSelect = document.getElementById('quickSectionSelect');
        const targetSelect = document.getElementById('quickTargetSelect');
        const selectedSecId = parseInt(secSelect.value, 10);

        targetSelect.innerHTML = '';
        const found = sectionsDataset.find(s => s.id === selectedSecId);
        if (found && found.sets.length > 0) {
            found.sets.forEach(set => {
                const opt = document.createElement('option');
                opt.value = set.id;
                const statusTag = set.is_unlocked ? ' [Unlocked]' : ' [Locked]';
                opt.textContent = set.name + statusTag;
                targetSelect.appendChild(opt);
            });
        } else {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'No sets available';
            targetSelect.appendChild(opt);
        }
    }

    // Execute Solo / Progressive / Revoke action
    function executeQuickAction(mode) {
        const sectionId = document.getElementById('quickSectionSelect').value;
        const targetId = document.getElementById('quickTargetSelect').value;

        if ((mode === 'solo' || mode === 'progressive_next') && !targetId) {
            alert('Please select a target set.');
            return;
        }

        const confirmMsg = mode === 'solo' 
            ? 'Assign ONLY this set exclusively and lock all sibling sets in this section?' 
            : (mode === 'progressive_next' ? 'Unlock this set progressively for the student?' : 'Lock all sets in this section?');

        if (!confirm(confirmMsg)) return;

        postQuickAssign(sectionId, targetId, mode);
    }

    // Quick Solo directly from set row button
    function quickSoloSubfolder(sectionId, targetId) {
        if (!confirm('Assign ONLY this set exclusively and lock all other sibling sets in this section?')) return;
        postQuickAssign(sectionId, targetId, 'solo');
    }

    // AJAX dispatcher for Quick Set Allocator
    function postQuickAssign(sectionId, targetId, mode) {
        const token = document.querySelector('input[name="_token"]').value;
        const alertBox = document.getElementById('ajaxAlertContainer');

        alertBox.innerHTML = `
            <div class="alert alert-info py-2">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Updating permissions in real-time...
            </div>
        `;

        fetch(quickAssignUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                section_folder_id: sectionId,
                target_folder_id: targetId || null,
                mode: mode,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertBox.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                // Update UI state without full reload
                applyLocalDomUpdate(sectionId, targetId, mode);
            } else {
                alertBox.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>${data.message || 'Error occurred.'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        })
        .catch(err => {
            alertBox.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Failed to update: ${err.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        });
    }

    // Update checkboxes and badges locally on successful AJAX quick assign
    function applyLocalDomUpdate(sectionId, targetId, mode) {
        sectionId = parseInt(sectionId, 10);
        targetId = targetId ? parseInt(targetId, 10) : null;

        if (mode === 'solo') {
            // Uncheck all other subfolders and their tests in this section
            document.querySelectorAll(`.perm-subfolder-cb.sub-of-${sectionId}`).forEach(cb => {
                const fId = parseInt(cb.dataset.folderId, 10);
                if (fId === targetId) {
                    cb.checked = true;
                    updateBadgeStatus(fId, true);
                    // Check its tests
                    document.querySelectorAll(`.test-of-folder-${fId}, .lesson-of-folder-${fId}`).forEach(t => t.checked = true);
                } else {
                    cb.checked = false;
                    updateBadgeStatus(fId, false);
                    document.querySelectorAll(`.test-of-folder-${fId}, .lesson-of-folder-${fId}`).forEach(t => t.checked = false);
                }
            });
        } else if (mode === 'progressive_next') {
            // Check target folder and its tests
            const targetCb = document.getElementById(`folder_${targetId}`);
            if (targetCb) targetCb.checked = true;
            updateBadgeStatus(targetId, true);
            document.querySelectorAll(`.test-of-folder-${targetId}, .lesson-of-folder-${targetId}`).forEach(t => t.checked = true);
        } else if (mode === 'revoke_section') {
            // Uncheck entire section
            document.querySelectorAll(`.sub-of-${sectionId}`).forEach(cb => {
                cb.checked = false;
                const fId = parseInt(cb.dataset.folderId, 10);
                updateBadgeStatus(fId, false);
            });
            document.querySelectorAll(`.test-of-sec-${sectionId}, .lesson-of-sec-${sectionId}`).forEach(t => t.checked = false);
        }

        // Also update local dataset
        const found = sectionsDataset.find(s => s.id === sectionId);
        if (found) {
            found.sets.forEach(set => {
                if (mode === 'solo') {
                    set.is_unlocked = (set.id === targetId);
                } else if (mode === 'progressive_next' && set.id === targetId) {
                    set.is_unlocked = true;
                } else if (mode === 'revoke_section') {
                    set.is_unlocked = false;
                }
            });
            onQuickSectionChange();
        }
    }

    function updateBadgeStatus(folderId, isUnlocked) {
        const badge = document.querySelector(`.badge-status-${folderId}`);
        if (badge) {
            if (isUnlocked) {
                badge.className = `badge bg-success ms-2 badge-status-${folderId}`;
                badge.textContent = 'Unlocked';
            } else {
                badge.className = `badge bg-secondary ms-2 badge-status-${folderId}`;
                badge.textContent = 'Locked';
            }
        }
    }

    // Synchronize subfolder checkbox toggle with its internal tests
    function onSubfolderToggle(folderId, isChecked) {
        document.querySelectorAll(`.test-of-folder-${folderId}, .lesson-of-folder-${folderId}`).forEach(cb => {
            cb.checked = isChecked;
        });
        updateBadgeStatus(folderId, isChecked);
    }

    // Tree Collapse / Expand
    function toggleSectionCollapse(sectionId) {
        const body = document.getElementById(`section-body-${sectionId}`);
        const icon = document.getElementById(`collapse-icon-${sectionId}`);
        if (!body) return;

        if (body.style.display === 'none') {
            body.style.display = 'block';
            if (icon) icon.className = 'bi bi-chevron-down me-2 text-muted transition-icon';
        } else {
            body.style.display = 'none';
            if (icon) icon.className = 'bi bi-chevron-right me-2 text-muted transition-icon';
        }
    }

    function expandAllSections() {
        document.querySelectorAll('.section-collapse-body').forEach(b => b.style.display = 'block');
        document.querySelectorAll('.transition-icon').forEach(i => i.className = 'bi bi-chevron-down me-2 text-muted transition-icon');
    }

    function collapseAllSections() {
        document.querySelectorAll('.section-collapse-body').forEach(b => b.style.display = 'none');
        document.querySelectorAll('.transition-icon').forEach(i => i.className = 'bi bi-chevron-right me-2 text-muted transition-icon');
    }

    // Grant or revoke entire section
    function grantSectionContent(secId) {
        document.querySelectorAll(`.sub-of-${secId}, .test-of-sec-${secId}, .lesson-of-sec-${secId}`).forEach(cb => cb.checked = true);
        const parentCb = document.getElementById(`folder_${secId}`);
        if (parentCb) parentCb.checked = true;
        document.querySelectorAll(`.sub-of-${secId}`).forEach(cb => updateBadgeStatus(cb.dataset.folderId, true));
    }

    function revokeSectionContent(secId) {
        document.querySelectorAll(`.sub-of-${secId}, .test-of-sec-${secId}, .lesson-of-sec-${secId}`).forEach(cb => cb.checked = false);
        const parentCb = document.getElementById(`folder_${secId}`);
        if (parentCb) parentCb.checked = false;
        document.querySelectorAll(`.sub-of-${secId}`).forEach(cb => updateBadgeStatus(cb.dataset.folderId, false));
    }

    // Bulk toggle all in the course
    function bulkToggleAll(checked) {
        document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = checked);
        document.querySelectorAll('.perm-subfolder-cb').forEach(cb => updateBadgeStatus(cb.dataset.folderId, checked));
    }

    // Real-time Tree Search Filter
    function filterTree() {
        const query = document.getElementById('treeSearchInput').value.toLowerCase().trim();
        const sectionCards = document.querySelectorAll('.section-tree-card');

        if (!query) {
            sectionCards.forEach(c => {
                c.style.display = '';
                c.querySelectorAll('.subfolder-row, .test-row, .lesson-row').forEach(r => r.style.display = '');
            });
            return;
        }

        // Expand sections automatically when searching
        expandAllSections();

        sectionCards.forEach(card => {
            const secTitle = card.querySelector('.section-title')?.textContent.toLowerCase() || '';
            let hasVisibleChild = false;

            // Search inside subfolders and tests
            const subfolderRows = card.querySelectorAll('.subfolder-row');
            subfolderRows.forEach(row => {
                const subText = row.textContent.toLowerCase();
                if (subText.includes(query) || secTitle.includes(query)) {
                    row.style.display = '';
                    hasVisibleChild = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // If section title matches query or has visible child, keep section card visible
            if (secTitle.includes(query) || hasVisibleChild) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function clearSearch() {
        document.getElementById('treeSearchInput').value = '';
        filterTree();
    }
</script>
@endpush