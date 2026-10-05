@extends('layouts.app')

@section('title', 'Status Tracker - TS Language Platform')

@section('content')
<style>
    /* Excel-like Grid Styles */
    .spreadsheet-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        padding-bottom: 10px; /* Space for scrollbar */
    }

    .spreadsheet-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        min-width: 900px; /* Force minimum width to trigger scroll on mobile */
    }
    
    .sheet-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .sheet-table th, .sheet-table td {
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        font-size: 0.95rem;
        vertical-align: middle;
    }

    .sheet-table tr:last-child td {
        border-bottom: none;
    }
    
    .sheet-table th {
        background: #f8fafc;
        padding: 16px;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        text-align: left;
        border-top: none;
    }
    
    .sheet-table th:first-child {
        border-top-left-radius: 12px;
    }

    .sheet-table th:last-child {
        border-top-right-radius: 12px;
        border-right: none;
    }
    
    .sheet-cell {
        padding: 12px 16px;
        min-height: 48px;
        cursor: text;
        transition: all 0.2s;
        outline: none;
        display: block;
        color: #334155;
    }

    .sheet-cell[contenteditable="false"] {
        cursor: default;
        background: #f8fafc;
        color: #94a3b8;
    }
    
    .sheet-cell:focus {
        background: #f0f9ff;
        box-shadow: inset 0 0 0 2px #3b82f6;
        color: #0f172a;
    }
    
    .sheet-cell:empty::before {
        content: attr(placeholder);
        color: #94a3b8;
        font-style: italic;
    }

    /* Native Time Picker Styling Override */
    input[type="time"].sheet-cell {
        appearance: none;
        -webkit-appearance: none;
        border: none;
        background: transparent;
        font-family: inherit;
        font-size: 0.95rem;
    }
    input[type="time"].sheet-cell::-webkit-calendar-picker-indicator {
        opacity: 0.6;
        cursor: pointer;
    }

    .date-cell {
        background-color: #fcfcfc;
        color: #0f172a;
        font-weight: 600;
        padding: 16px;
    }

    .day-cell {
        background-color: #fcfcfc;
        color: #64748b;
        font-weight: 500;
        padding: 16px;
    }

    /* Status Indicators */
    .status-saving {
        opacity: 0.5;
        background-color: #f1f5f9;
    }
    
    .status-saved {
        position: relative;
    }
    
    .status-saved::after {
        content: '';
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 6px; 
        height: 6px; 
        background: #10b981;
        border-radius: 50%;
    }

    /* Buttons */
    .btn-add-topic {
        width: 100%;
        border: 1px dashed #cbd5e1;
        background: #fff;
        color: #64748b;
        padding: 8px;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-add-topic:hover {
        background: #f8fafc;
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .btn-add-topic:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .delete-topic-btn {
        background: transparent;
        border: none;
        color: #cbd5e1;
        cursor: pointer;
        transition: color 0.2s;
        padding: 8px;
    }

    .delete-topic-btn:hover {
        color: #ef4444;
    }

    .delete-topic-btn:disabled {
        opacity: 0;
        cursor: default;
    }

    .total-time-badge {
        display: inline-block;
        padding: 4px 8px;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 8px;
    }

    .locked-badge {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 4px;
        display: block;
        text-align: center;
    }
</style>

<div class="container py-5">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 mb-md-5 gap-3">
        <div>
            <h1 class="h2 fw-bold text-dark mb-2">Status Tracker</h1>
            <p class="text-muted mb-0">Record your daily learning journey efficiently.</p>
        </div>
        
        <div class="d-flex gap-3 align-items-center bg-white p-2 rounded-pill shadow-sm border align-self-start align-self-md-auto">
            <a href="{{ route('student.status.index', ['month' => $date->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-light btn-sm rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-chevron-left"></i>
            </a>
            <span class="fw-bold text-dark mx-2" style="min-width: 120px; text-align: center;">{{ $date->format('F Y') }}</span>
            <a href="{{ route('student.status.index', ['month' => $date->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-light btn-sm rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Tracker Table -->
    <div class="spreadsheet-wrapper">
        <div class="spreadsheet-container">
            <table class="sheet-table">
            <thead>
                <tr>
                    <th style="width: 120px;">Date</th>
                    <th style="width: 140px;">Day</th>
                    <th>Topics Learned</th>
                    <th style="width: 110px;">Start</th>
                    <th style="width: 110px;">End</th>
                    <th style="width: 110px;">Duration</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($days as $day)
                    @php 
                        $isToday = $day['is_today'] ?? false;
                        $dateStr = $day['date']->format('Y-m-d');
                        $status = $day['status'];
                        $topics = $status ? $status->topics : collect();
                        $rowCount = $topics->count() + 1; 
                        $editable = $isToday ? 'true' : 'false';
                    @endphp
                    
                    {{-- Row 1: First Topic OR Empty State if no topics --}}
                    <tr data-date="{{ $dateStr }}">
                        {{-- Date & Day Columns (Rowspanned) --}}
                        <td rowspan="{{ $rowCount }}" class="date-cell text-center" style="vertical-align: top;">
                            <div class="d-flex flex-column align-items-center">
                                <span class="fs-5">{{ $day['date']->format('d') }}</span>
                                <span class="small text-muted text-uppercase">{{ $day['date']->format('M') }}</span>
                            </div>
                            <div class="total-time-badge d-none" id="total-badge-{{ $dateStr }}">
                                <!-- JS Populated -->
                            </div>
                            @if(!$isToday)
                                <span class="locked-badge"><i class="bi bi-lock-fill"></i> Locked</span>
                            @endif
                        </td>
                        <td rowspan="{{ $rowCount }}" class="day-cell text-center" style="vertical-align: top;">
                            {{ $day['date']->format('l') }}
                        </td>

                        @if($topics->count() > 0)
                            {{-- Render First Topic --}}
                            @php $firstTopic = $topics->first(); @endphp
                            <td>
                                <div class="sheet-cell" contenteditable="{{ $editable }}" 
                                     placeholder="{{ $isToday ? 'What did you learn?' : '' }}"
                                     data-topic-id="{{ $firstTopic->id }}" 
                                     data-field="topic">{{ $firstTopic->topic }}</div>
                            </td>
                            <td>
                                <input type="time" class="sheet-cell time-input w-100" 
                                     {{ $isToday ? '' : 'disabled' }}
                                     data-topic-id="{{ $firstTopic->id }}" 
                                     data-field="starting_time"
                                     onchange="calculateDurationRow(this); saveCell(this)"
                                     value="{{ $firstTopic->starting_time }}">
                            </td>
                            <td>
                                <input type="time" class="sheet-cell time-input w-100" 
                                     {{ $isToday ? '' : 'disabled' }}
                                     data-topic-id="{{ $firstTopic->id }}" 
                                     data-field="ending_time"
                                     onchange="calculateDurationRow(this); saveCell(this)"
                                     value="{{ $firstTopic->ending_time }}">
                            </td>
                            <td>
                                <div class="sheet-cell duration-cell text-muted bg-light" 
                                     style="cursor: default;"
                                     data-topic-id="{{ $firstTopic->id }}" 
                                     data-field="duration">{{ $firstTopic->duration }}</div>
                            </td>
                            <td class="text-center">
                                <button class="delete-topic-btn" onclick="deleteTopic('{{ $firstTopic->id }}')" {{ $isToday ? '' : 'disabled' }}>
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        @else
                            {{-- Render Empty "Add" Row (This acts as the first and only row if no topics) --}}
                             <td colspan="5" class="p-2 bg-light">
                                <button class="btn-add-topic" onclick="addTopic('{{ $dateStr }}')" {{ $isToday ? '' : 'disabled' }}>
                                    <i class="bi bi-plus-lg"></i> {{ $isToday ? 'Add Entry' : 'Locked' }}
                                </button>
                            </td>
                        @endif
                    </tr>

                    {{-- Remaining Topics (Loop from index 1) --}}
                    @if($topics->count() > 1)
                        @foreach($topics->slice(1) as $topic)
                        <tr data-date="{{ $dateStr }}">
                            <td>
                                <div class="sheet-cell" contenteditable="{{ $editable }}" 
                                     placeholder="{{ $isToday ? 'What did you learn?' : '' }}"
                                     data-topic-id="{{ $topic->id }}" 
                                     data-field="topic">{{ $topic->topic }}</div>
                            </td>
                            <td>
                                <input type="time" class="sheet-cell time-input w-100" 
                                     {{ $isToday ? '' : 'disabled' }}
                                     data-topic-id="{{ $topic->id }}" 
                                     data-field="starting_time"
                                     onchange="calculateDurationRow(this); saveCell(this)"
                                     value="{{ $topic->starting_time }}">
                            </td>
                            <td>
                                <input type="time" class="sheet-cell time-input w-100" 
                                     {{ $isToday ? '' : 'disabled' }}
                                     data-topic-id="{{ $topic->id }}" 
                                     data-field="ending_time"
                                     onchange="calculateDurationRow(this); saveCell(this)"
                                     value="{{ $topic->ending_time }}">
                            </td>
                            <td>
                                <div class="sheet-cell duration-cell text-muted bg-light" 
                                     style="cursor: default;"
                                     data-topic-id="{{ $topic->id }}" 
                                     data-field="duration">{{ $topic->duration }}</div>
                            </td>
                            <td class="text-center">
                                <button class="delete-topic-btn" onclick="deleteTopic('{{ $topic->id }}')" {{ $isToday ? '' : 'disabled' }}>
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @endif

                    {{-- Add Button Row (Only if topics > 0, because if 0, the first row IS the add row) --}}
                    @if($topics->count() > 0)
                    <tr data-date="{{ $dateStr }}">
                        <td colspan="5" class="p-2">
                             <button class="btn-add-topic" onclick="addTopic('{{ $dateStr }}')" {{ $isToday ? '' : 'disabled' }}>
                                <i class="bi bi-plus-lg"></i> {{ $isToday ? 'Add another topic' : 'Locked' }}
                            </button>
                        </td>
                    </tr>
                    @endif

                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function() {
        calculateAllTotals();
        
        const tableBody = document.querySelector('.sheet-table tbody');

        // Event Delegation for Saves
        tableBody.addEventListener('blur', function(e) {
            if (e.target.classList.contains('sheet-cell') && e.target.isContentEditable) {
                // Ensure editability just in case
                 if (e.target.getAttribute('contenteditable') === 'true') {
                    saveCell(e.target);
                 }
            }
        }, true);

        // Enter key behavior
        tableBody.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('sheet-cell') && e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                e.target.blur();
            }
        });
    });

    function addTopic(date) {
        fetch('{{ route("student.status.topic.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ date: date })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload(); 
            } else {
                alert(data.message || 'Error occurred');
            }
        });
    }

    function saveCell(cell) {
        if (!cell.dataset.topicId) return;

        const topicId = cell.dataset.topicId;
        const field = cell.dataset.field;
        // If it's an input element, use .value, otherwise use textContent
        const value = cell.tagName === 'INPUT' ? cell.value.trim() : cell.textContent.trim();
        
        cell.classList.add('status-saving');

        fetch('{{ route("student.status.topic.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                topic_id: topicId,
                field: field,
                value: value
            })
        })
        .then(response => response.json())
        .then(data => {
            cell.classList.remove('status-saving');
            if (data.success) {
                cell.classList.add('status-saved');
                setTimeout(() => cell.classList.remove('status-saved'), 2000);
            } else {
                cell.style.backgroundColor = '#fee2e2';
                // Potentially revert change or alert if blocked by server validation
                if (data.message) alert(data.message);
            }
        })
        .catch(err => {
            cell.classList.remove('status-saving');
            cell.style.backgroundColor = '#fee2e2';
        });
    }

    function deleteTopic(topicId) {
        if (!confirm('Area you sure you want to remove this topic?')) return;

        fetch('{{ route("student.status.topic.delete") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ topic_id: topicId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        });
    }

    function calculateDurationRow(input) {
        // Only run if the element is editable or is a time input
        if (input.getAttribute('contenteditable') !== 'true' && input.tagName !== 'INPUT') return;

        const row = input.closest('tr');
        const startInput = row.querySelector('[data-field="starting_time"]');
        const endInput = row.querySelector('[data-field="ending_time"]');
        const durationCell = row.querySelector('[data-field="duration"]');
        
        if (!startInput || !endInput || !durationCell) return;

        const topicId = durationCell.dataset.topicId;
        
        // Use value if it's an input element
        const start = startInput.tagName === 'INPUT' ? startInput.value.trim() : startInput.textContent.trim();
        const end = endInput.tagName === 'INPUT' ? endInput.value.trim() : endInput.textContent.trim();

        if (start && end) {
            // Flexible parsing
            const startTime = moment(start, ["HH:mm", "H:mm", "HHmm"]);
            const endTime = moment(end, ["HH:mm", "H:mm", "HHmm"]);

            if (startTime.isValid() && endTime.isValid()) {
                let duration = moment.duration(endTime.diff(startTime));
                
                // Handle crossing midnight
                if (duration.asMinutes() < 0) {
                     duration = moment.duration(endTime.add(1, 'd').diff(startTime));
                }

               let hours = Math.floor(duration.asHours());
               let minutes = Math.floor(duration.asMinutes()) % 60;
               
               const durationStr = `${hours}h ${minutes}m`;
               durationCell.textContent = durationStr;
               
               saveDuration(topicId, durationStr);
               
               // Update Daily Total
               const dateStr = row.getAttribute('data-date');
               if (dateStr) updateDailyTotal(dateStr);
            }
        }
    }
    
    function saveDuration(topicId, value) {
        fetch('{{ route("student.status.topic.update") }}', {
             method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                topic_id: topicId,
                field: 'duration',
                value: value
            })
        });
    }

    function updateDailyTotal(dateStr) {
        const rows = document.querySelectorAll(`tr[data-date="${dateStr}"]`);
        let totalMinutes = 0;

        rows.forEach(row => {
            const durationCell = row.querySelector('[data-field="duration"]');
            if (durationCell) {
                const text = durationCell.textContent.trim();
                 const match = text.match(/(\d+)h\s*(\d+)m/);
                 if (match) {
                     totalMinutes += (parseInt(match[1]) * 60) + parseInt(match[2]);
                 }
            }
        });

        const totalHours = Math.floor(totalMinutes / 60);
        const mins = totalMinutes % 60;
        
        const badge = document.getElementById(`total-badge-${dateStr}`);
        if (badge) {
            if (totalMinutes > 0) {
                badge.textContent = `${totalHours}h ${mins}m`;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }

    function calculateAllTotals() {
        const uniqueDates = [...new Set([...document.querySelectorAll('tr[data-date]')].map(r => r.dataset.date))];
        uniqueDates.forEach(date => updateDailyTotal(date));
    }
</script>
@endpush
@endsection
