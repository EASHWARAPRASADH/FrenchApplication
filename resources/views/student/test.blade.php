@extends('layouts.app')

@section('title', $test->title . ' - TS Language Platform')
@section('hideNavAndFooter', 'true')

@section('content')
    <style>
        /* Modern Test Taking Interface - Professional Design */
        body {
            background: #F8FAFC;
        }

        .test-header {
            background: linear-gradient(135deg, var(--brand-primary, #0066CC) 0%, var(--brand-primary-dark, #0052A3) 100%);
            color: white;
            padding: 1.25rem 0;
            border-radius: 0 0 16px 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 102, 204, 0.15);
        }

        .test-container {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 1.5rem;
            min-height: calc(100vh - 200px);
            padding-bottom: 85px;
        }

        .test-content {
            background: white;
            border-radius: 14px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
            height: fit-content;
        }

        .question-card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.875rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .question-number {
            background: linear-gradient(135deg, var(--brand-primary, #0066CC) 0%, var(--brand-primary-dark, #0052A3) 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        .question-type {
            background: rgba(0, 102, 204, 0.08);
            color: var(--brand-primary, #0066CC);
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .question-text {
            font-size: 1.2rem;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .question-media {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .question-media img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .question-media video {
            max-width: 100%;
            border-radius: 8px;
        }

        .audio-container {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border-radius: 12px;
            border: 2px solid #fdba74;
        }

        .audio-container audio {
            margin-top: 1rem;
            border-radius: 8px;
        }

        .options-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .option-item {
            margin-bottom: 0.75rem;
            padding: 0.875rem 1.125rem;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #ffffff;
            display: flex;
            align-items: center;
            user-select: none;
        }

        .option-item:hover {
            border-color: var(--brand-primary, #0066CC);
            background: #F0F7FF;
        }

        .option-item.selected {
            border-color: var(--brand-primary, #0066CC);
            background: #EBF5FF;
            color: #0F172A;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
            font-weight: 600;
        }

        .option-radio {
            margin-right: 0.875rem;
            width: 18px;
            height: 18px;
            accent-color: var(--brand-primary, #0066CC);
            cursor: pointer;
        }

        .option-text {
            flex: 1;
            font-weight: 500;
        }

        .fill-blanks-input {
            display: inline-block;
            min-width: 100px;
            padding: 0.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            margin: 0 0.25rem;
            text-align: center;
            font-weight: 500;
        }

        .fill-blanks-input:focus {
            outline: none;
            border-color: var(--brand-primary, #0066CC);
            background: #f0f7ff;
        }

        .test-sidebar {
            background: white;
            border-radius: 14px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 1.25rem;
            height: fit-content;
            position: sticky;
            top: 1.25rem;
        }

        .timer-display {
            text-align: center;
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            border: 1px solid #FCD34D;
        }

        .timer-icon {
            font-size: 1.5rem;
            color: #D97706;
            margin-bottom: 0.25rem;
        }

        .timer-text {
            font-size: 1.35rem;
            font-weight: 700;
            color: #92400E;
            font-variant-numeric: tabular-nums;
        }

        .timer-label {
            font-size: 0.8rem;
            color: #A16207;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 0.15rem;
            font-weight: 600;
        }

        .question-navigator {
            margin-bottom: 1.25rem;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.4rem;
        }

        .nav-question {
            width: 38px;
            height: 38px;
            border: 1.5px solid #E2E8F0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: white;
        }

        .nav-question:hover {
            border-color: var(--brand-primary, #0066CC);
            background: rgba(0, 102, 204, 0.06);
        }

        .nav-question.current {
            border-color: var(--brand-primary, #0066CC);
            background: linear-gradient(135deg, var(--brand-primary, #0066CC) 0%, var(--brand-primary-dark, #0052A3) 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 102, 204, 0.25);
        }

        .nav-question.answered {
            border-color: #10B981;
            background: #10B981;
            color: white;
        }

        /* Sticky bottom action bar */
        .test-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: #ffffff;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
            border-top: 1px solid #E2E8F0;
            padding: 0.75rem 1.5rem;
        }

        .test-info {
            background: #f8faff;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .test-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-test {
            flex: 1;
            padding: 0.75rem;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-previous {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-previous:hover:not(:disabled) {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-next {
            background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
            color: white;
        }

        .btn-next:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .warning-low-time {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @media (max-width: 768px) {
            .test-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .test-sidebar {
                position: static;
                order: -1;
            }

            .nav-grid {
                grid-template-columns: repeat(8, 1fr);
            }

            .test-actions {
                position: fixed;
                bottom: 1rem;
                left: 1rem;
                right: 1rem;
                background: white;
                padding: 1rem;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                z-index: 1000;
            }
        }

        /* Expression Ecrite Styles */
        .accent-toolbar {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            border-bottom: none;
        }

        .accent-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 4px;
            cursor: pointer;
            font-family: monospace;
            font-size: 1.1rem;
            transition: all 0.2s;
        }

        .accent-btn:hover {
            background: #e9ecef;
            transform: translateY(-1px);
        }

        .word-counter {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.5rem;
            text-align: right;
        }

        .word-counter.limit-exceeded {
            color: #dc3545;
        }

        /* Pre-Test Instructions Modal - Professional Light Theme */
        .instructions-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 2rem;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .instructions-content {
            background: #FFFFFF;
            border-radius: 24px;
            max-width: 680px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12), 0 8px 16px rgba(15, 23, 42, 0.08);
            animation: slideUp 0.4s ease-out;
            border: 1px solid rgba(79, 70, 229, 0.08);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .instructions-header {
            background: linear-gradient(135deg, var(--brand-primary, #0066CC) 0%, var(--brand-primary-dark, #0052A3) 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }

        .instructions-icon {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2rem;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .instructions-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.625rem;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        .instructions-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.0625rem;
            font-weight: 500;
        }

        .instructions-body {
            padding: 2.5rem 2rem;
        }

        .test-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .detail-card {
            background: linear-gradient(135deg, #FAFBFF 0%, #F8FAFC 100%);
            padding: 1.5rem 1.25rem;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(0, 102, 204, 0.1);
            transition: all 0.3s ease;
        }

        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 102, 204, 0.1);
            border-color: rgba(0, 102, 204, 0.2);
        }

        .detail-icon {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 36px;
        }

        .detail-value {
            font-size: 1.625rem;
            font-weight: 700;
            color: var(--brand-primary, #0066CC);
            margin-bottom: 0.25rem;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #64748B;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .instructions-section-title {
            color: #0F172A;
            font-weight: 700;
            font-size: 1.125rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .instructions-section-title::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
            border-radius: 4px;
        }

        .instructions-list {
            list-style: none;
            padding: 0;
            margin-bottom: 0;
        }

        .instruction-item {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            padding: 1.25rem;
            margin-bottom: 0.875rem;
            background: linear-gradient(135deg, #FAFBFF 0%, #F8FAFC 100%);
            border-radius: 14px;
            border: 1px solid rgba(79, 70, 229, 0.08);
            transition: all 0.2s ease;
        }

        .instruction-item:hover {
            border-color: rgba(79, 70, 229, 0.2);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08);
            transform: translateX(4px);
        }

        .instruction-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            color: #4F46E5;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.125rem;
            border: 1px solid rgba(79, 70, 229, 0.1);
        }

        .instruction-text {
            flex: 1;
            color: #475569;
            line-height: 1.6;
            font-size: 0.9375rem;
            padding-top: 0.25rem;
        }

        .instructions-footer {
            padding: 0 2rem 2.5rem;
        }

        .btn-start-test {
            background: linear-gradient(135deg, var(--brand-primary, #0066CC) 0%, var(--brand-primary-dark, #0052A3) 100%);
            color: white;
            width: 100%;
            padding: 1.125rem;
            border: none;
            border-radius: 12px;
            font-size: 1.0625rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(0, 102, 204, 0.25);
        }

        .btn-start-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(79, 70, 229, 0.3);
        }

        .btn-start-test:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .test-details {
                grid-template-columns: 1fr;
            }

            .instructions-modal {
                padding: 1rem;
            }
        }
        }
    </style>


    <!-- Pre-Test Instructions Modal -->
    <div class="instructions-modal" id="instructionsModal">
        <div class="instructions-content">
            <div class="instructions-header">
                <div class="instructions-icon">
                    <i class="bi bi-file-earmark-text text-white"></i>
                </div>
                <h2 class="instructions-title">{{ $test->title }}</h2>
                <p class="instructions-subtitle">Please read the instructions carefully before starting</p>
            </div>

            <div class="instructions-body">
                <div class="test-details">
                    <div class="detail-card">
                        <div class="detail-icon"><i class="bi bi-ui-checks text-primary"></i></div>
                        <div class="detail-value">{{ $totalQuestions }}</div>
                        <div class="detail-label">Questions</div>
                    </div>
                    @if($test->time_limit)
                        <div class="detail-card">
                            <div class="detail-icon"><i class="bi bi-stopwatch text-primary"></i></div>
                            <div class="detail-value">{{ $test->time_limit }}</div>
                            <div class="detail-label">Minutes</div>
                        </div>
                    @else
                        <div class="detail-card">
                            <div class="detail-icon"><i class="bi bi-infinity text-primary"></i></div>
                            <div class="detail-value">Unlimited</div>
                            <div class="detail-label">Duration</div>
                        </div>
                    @endif
                    <div class="detail-card">
                        <div class="detail-icon"><i class="bi bi-check2-circle text-success"></i></div>
                        <div class="detail-value">{{ $test->passing_score }}%</div>
                        <div class="detail-label">Passing Score</div>
                    </div>
                    <div class="detail-card">
                        <div class="detail-icon"><i class="bi bi-arrow-repeat text-primary"></i></div>
                        <div class="detail-value">{{ $test->max_attempts ?? 'Unlimited' }}</div>
                        <div class="detail-label">Attempts</div>
                    </div>
                </div>

                <h5 class="instructions-section-title">Important Instructions</h5>
                <ul class="instructions-list">
                    <li class="instruction-item">
                        <div class="instruction-icon">
                            <i class="bi bi-check2"></i>
                        </div>
                        <div class="instruction-text">
                            Read each question carefully before selecting your answer
                        </div>
                    </li>
                    <li class="instruction-item">
                        <div class="instruction-icon">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div class="instruction-text">
                            You can navigate between questions using the Next/Previous buttons
                        </div>
                    </li>
                    <li class="instruction-item">
                        <div class="instruction-icon">
                            <i class="bi bi-grid-3x3"></i>
                        </div>
                        <div class="instruction-text">
                            Use the question navigator on the right to jump to any question
                        </div>
                    </li>
                    @if($test->time_limit)
                        <li class="instruction-item">
                            <div class="instruction-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="instruction-text">
                                The test will automatically submit when time runs out
                            </div>
                        </li>
                    @endif
                    <li class="instruction-item">
                        <div class="instruction-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="instruction-text">
                            Make sure to click "Submit Test" when you're done - incomplete tests won't be graded
                        </div>
                    </li>
                </ul>
            </div>

            <div class="instructions-footer">
                <button class="btn-start-test" onclick="startTest()">
                    <i class="bi bi-play-circle me-2"></i>
                    Start Test
                </button>
            </div>
        </div>
    </div>

    <div class="test-header">

        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('student.course.show', $test->course) }}"
                                    class="text-white-50 text-decoration-none">{{ $test->course->title }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $test->title }}</li>
                        </ol>
                    </nav>
                    <h1 class="mb-1 text-white">{{ $test->title }}</h1>
                    <p class="mb-0 text-white-50">{{ $test->description ?? 'Test your knowledge and skills' }}</p>
                </div>
                <div class="col-md-4 text-md-end text-white">
                    <div class="d-flex justify-content-md-end gap-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $currentQuestion + 1 }}</div>
                            <small class="opacity-75">of {{ $totalQuestions }}</small>
                        </div>
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $test->passing_score }}%</div>
                            <small class="opacity-75">to Pass</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if(($test->type ?? 'standard') === 'expression_ecrite')
            @include('student.partials.test-expression-ecrite')
        @else
            <div class="test-container">
                <!-- Main Test Content -->
                <div class="test-content">
                    @if($test->questions && $test->questions->count() > 0)
                        @foreach($test->questions->sortBy('order') as $index => $question)
                            <div class="question-card" id="question-{{ $index }}" style="{{ $index === 0 ? '' : 'display: none;' }}">
                                <div class="question-header">
                                    <div class="question-number">{{ $index + 1 }}</div>
                                    <div class="question-type">{{ strtoupper(str_replace('_', ' ', $question->type)) }}</div>
                                </div>

                                @if($question->type !== 'fill_blanks')
                                    <div class="question-text">{{ $question->question_text }}</div>
                                @endif

                                @if(in_array($question->type, ['passage_mcq', 'expression_ecrite']) && $question->passage)
                                    <div class="question-passage">
                                        <div class="passage-box p-3 mb-3 border rounded bg-light">
                                            {!! nl2br(e($question->passage)) !!}
                                        </div>
                                    </div>
                                @endif


                                <!-- Question Media -->
                                @if(in_array($question->type, ['mcq_image', 'image_mcq', 'audio', 'image_audio_mcq', 'video']) && $question->question_media)
                                    <div class="question-media">
                                        @if($question->type === 'image_audio_mcq')
                                            @php
                                                $raw = $question->question_media;
                                                $media = null;
                                                if (is_string($raw) && preg_match('/^\s*\{/', $raw)) {
                                                    $media = json_decode($raw, true);
                                                }
                                                $imageSrc = $media['image'] ?? null;
                                                $audioSrc = $media['audio'] ?? null;
                                                if ($imageSrc && !preg_match('/^https?:\/\//', $imageSrc)) {
                                                    $imageSrc = asset($imageSrc);
                                                }
                                                if ($audioSrc && !preg_match('/^https?:\/\//', $audioSrc)) {
                                                    $audioSrc = asset($audioSrc);
                                                }
                                            @endphp
                                            @if($imageSrc)
                                                <img src="{{ $imageSrc }}" alt="Question Image" class="mb-3" oncontextmenu="return false;" ondragstart="return false;">
                                            @endif
                                            @if($audioSrc)
                                                <div class="audio-container">
                                                    <i class="bi bi-volume-up fs-3 text-warning mb-2"></i>
                                                    <audio controls controlsList="nodownload noplaybackrate" oncontextmenu="return false;" class="w-100">
                                                        <source src="{{ $audioSrc }}" type="audio/mpeg">
                                                        <source src="{{ $audioSrc }}" type="audio/wav">
                                                        <source src="{{ $audioSrc }}" type="audio/ogg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                            @endif
                                        @elseif($question->type === 'audio')
                                            @php
                                                $mediaSrc = $question->question_media;
                                                if ($mediaSrc && !preg_match('/^https?:\/\//', $mediaSrc)) {
                                                    $mediaSrc = asset($mediaSrc);
                                                }
                                            @endphp
                                            <div class="audio-container">
                                                <i class="bi bi-volume-up fs-3 text-warning mb-2"></i>
                                                <audio controls controlsList="nodownload noplaybackrate" oncontextmenu="return false;" class="w-100">
                                                    <source src="{{ $mediaSrc }}" type="audio/mpeg">
                                                    <source src="{{ $mediaSrc }}" type="audio/wav">
                                                    <source src="{{ $mediaSrc }}" type="audio/ogg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                        @elseif($question->type === 'video')
                                            @php
                                                $mediaSrc = $question->question_media;
                                                if ($mediaSrc && !preg_match('/^https?:\/\//', $mediaSrc)) {
                                                    $mediaSrc = asset($mediaSrc);
                                                }
                                            @endphp
                                            @if(str_contains($mediaSrc, 'youtube.com') || str_contains($mediaSrc, 'youtu.be'))
                                                <iframe width="560" height="315" src="{{ $mediaSrc }}" frameborder="0" allowfullscreen></iframe>
                                            @else
                                                <video controls>
                                                    <source src="{{ $mediaSrc }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @endif
                                        @else
                                            @php
                                                $mediaSrc = $question->question_media;
                                                if ($mediaSrc && !preg_match('/^https?:\/\//', $mediaSrc)) {
                                                    $mediaSrc = asset($mediaSrc);
                                                }
                                            @endphp
                                            <img src="{{ $mediaSrc }}" alt="Question Image" oncontextmenu="return false;" ondragstart="return false;">
                                        @endif
                                    </div>
                                @endif

                                <!-- Expression Ecrite Writing Area -->
                                @if($question->type === 'expression_ecrite')
                                    <div class="writing-area mt-4">
                                        <!-- Accent Helper Toolbar -->
                                        <div
                                            class="accent-toolbar d-flex align-items-center flex-wrap gap-2 p-2 bg-light border rounded-top">
                                            <button type="button" class="accent-btn btn btn-sm btn-outline-secondary fw-bold"
                                                id="caps-btn-{{ $question->id }}" onclick="toggleCaps('{{ $question->id }}')"
                                                title="Toggle Caps Lock" style="width: auto; padding: 0 10px;">
                                                Caps
                                            </button>
                                            <div class="vr mx-2"></div>

                                            <!-- Dynamic buttons container -->
                                            <div id="accent-buttons-{{ $question->id }}" class="d-flex gap-1 flex-wrap">
                                                @foreach(['é', 'è', 'à', 'ù', 'â', 'ê', 'î', 'ô', 'û', 'ë', 'ï', 'ü', 'ÿ', 'ç', '«', '»', '’'] as $char)
                                                    <button type="button" class="accent-btn btn btn-sm btn-light border" data-char="{{ $char }}"
                                                        onclick="insertCharacter(this, 'answer-{{ $question->id }}')" title="Insert {{ $char }}"
                                                        style="width: 35px; height: 35px; font-size: 1.1rem;">
                                                        {{ $char }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Text Area -->
                                        <textarea class="form-control border-top-0 rounded-0 rounded-bottom p-3"
                                            id="answer-{{ $question->id }}" rows="15" placeholder="Type your response here..."
                                            oninput="updateWordCount(this, {{ $question->min_words ?? 0 }}, {{ $question->max_words ?? 0 }})"></textarea>

                                        <!-- Word Counter -->
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                @if($question->min_words && $question->max_words)
                                                    Target: {{ $question->min_words }} - {{ $question->max_words }} words
                                                @endif
                                            </small>
                                            <div class="word-counter" id="word-count-{{ $question->id }}">
                                                0 words
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Question Options -->
                                @if(in_array($question->type, ['mcq', 'mcq_image', 'audio', 'video', 'image_mcq', 'passage_mcq', 'image_audio_mcq']))
                                    @if($question->options && $question->options->count() > 0)
                                        <ul class="options-list">
                                            @foreach($question->options->sortBy('order')->values() as $optionIndex => $option)
                                                <li class="option-item" onclick="selectOption({{ $index }}, {{ $optionIndex }})">
                                                    <input type="radio" class="option-radio" name="question_{{ $question->id }}"
                                                        value="{{ $optionIndex }}">
                                                    @if(!empty($option->option_image))
                                                        <img src="{{ preg_match('/^https?:\\/\\//', $option->option_image ?? '') ? $option->option_image : asset($option->option_image) }}"
                                                            alt="Option Image" class="option-image me-2" style="max-height: 80px;" oncontextmenu="return false;" ondragstart="return false;">
                                                    @endif
                                                    <span class="option-text">{{ $option->option_text }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <strong>No options available for this question.</strong>
                                            <br><small>This question may need to be recreated by an administrator.</small>
                                        </div>
                                    @endif
                                @endif

                                <!-- Fill in the Blanks -->
                                @if($question->type === 'fill_blanks')
                                    <div class="fill-blanks-container">
                                        @php
                                            $questionText = $question->question_text;
                                            $blanks = $question->correct_answer ?? [];
                                            $parts = explode('_____', $questionText);
                                        @endphp

                                        <div class="fill-blanks-text">
                                            @foreach($parts as $partIndex => $part)
                                                {{ $part }}
                                                @if($partIndex < count($parts) - 1)
                                                    <input type="text" class="fill-blanks-input" data-question="{{ $index }}"
                                                        data-blank="{{ $partIndex }}" placeholder="...">
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                            <h4 class="mt-3">No Questions Available</h4>
                            <p class="text-muted">This test doesn't have any questions yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="test-sidebar">
                    <!-- Timer -->
                    <div class="timer-display" id="timerDisplay">
                        <i class="bi bi-clock timer-icon"></i>
                        @if($test->time_limit)
                            <div class="timer-text" id="timerText">{{ $test->time_limit }}:00</div>
                            <div class="timer-label">Time Remaining</div>
                        @else
                            <div class="timer-text" id="timerText">00:00</div>
                            <div class="timer-label">Time Elapsed</div>
                        @endif
                    </div>

                    <!-- Question Navigator -->
                    <div class="question-navigator">
                        <h6 class="mb-3">Questions</h6>
                        <div class="nav-grid">
                            @for($i = 0; $i < $totalQuestions; $i++)
                                <div class="nav-question {{ $i === 0 ? 'current' : '' }}" onclick="goToQuestion({{ $i }})">
                                    {{ $i + 1 }}
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Test Info -->
                    <div class="test-info">
                        <div class="info-item">
                            <span>Questions:</span>
                            <span>{{ $totalQuestions }}</span>
                        </div>
                        <div class="info-item">
                            <span>Duration:</span>
                            <span>{{ $test->time_limit ?? 'Unlimited' }} min</span>
                        </div>
                        <div class="info-item">
                            <span>Passing Score:</span>
                            <span>{{ $test->passing_score }}%</span>
                        </div>
                        <div class="info-item">
                            <span>Attempts:</span>
                            <span>{{ $currentAttempt }} of {{ $test->max_attempts ?? 'Unlimited' }}</span>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="test-actions">
                        <button class="btn btn-test btn-previous" onclick="previousQuestion()" disabled>
                            <i class="bi bi-arrow-left me-1"></i>
                            Previous
                        </button>
                        <button class="btn btn-test btn-next" onclick="nextQuestion()">
                            Next
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-submit" onclick="submitTest()">
                        <i class="bi bi-check-circle me-2"></i>
                        Submit Test
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Sticky Bottom Navigation Bar for Focus Mode -->
    <div class="test-bottom-bar shadow-sm">
        <div class="container d-flex align-items-center justify-content-between p-0">
            <button class="btn btn-outline-secondary px-3 py-2 fw-medium" id="bottomPrevBtn" onclick="previousQuestion()" disabled>
                <i class="bi bi-arrow-left me-1"></i>Previous
            </button>
            <div class="text-center">
                <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-semibold">
                    Question <span id="currentQuestionNum">1</span> of {{ $totalQuestions }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary px-3 py-2 fw-medium" id="bottomNextBtn" onclick="nextQuestion()">
                    Next<i class="bi bi-arrow-right ms-1"></i>
                </button>
                <button class="btn btn-success px-3 py-2 fw-medium" onclick="submitTest()">
                    <i class="bi bi-check2-circle me-1"></i>Submit Test
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentQuestion = 0;
            const totalQuestions = {{ $totalQuestions }};

            let answers = {};

            // Timer Variables
            const timeLimitMinutes = {{ $test->time_limit ?? 0 }};
            const startTime = Date.now();
            let timerInterval;

            // Timer functionality
            function formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m}:${s.toString().padStart(2, '0')}`;
            }

            function updateTimer() {
                const now = Date.now();
                const elapsedSeconds = Math.floor((now - startTime) / 1000);

                if (timeLimitMinutes > 0) {
                    // Countdown
                    const totalSeconds = timeLimitMinutes * 60;
                    const remaining = Math.max(0, totalSeconds - elapsedSeconds);
                    document.getElementById('timerText').textContent = formatTime(remaining);

                    if (remaining <= 0) {
                        clearInterval(timerInterval);
                        alert('Time is up! Submitting test automatically.');
                        submitTest();
                    } else if (remaining <= 300) { // 5 minutes warning
                        document.getElementById('timerDisplay').classList.add('warning-low-time');
                    }
                } else {
                    // Count up
                    document.getElementById('timerText').textContent = formatTime(elapsedSeconds);
                }
            }

            // Start timer when page loads
            document.addEventListener('DOMContentLoaded', () => {
                updateTimer(); // Initial call
                timerInterval = setInterval(updateTimer, 1000);
            });

            // Question navigation
            function goToQuestion(questionIndex) {
                // Hide current question
                document.querySelector(`#question-${currentQuestion}`).style.display = 'none';

                // Update navigation
                document.querySelectorAll('.nav-question').forEach(nav => nav.classList.remove('current'));
                document.querySelectorAll('.nav-question')[questionIndex].classList.add('current');

                // Show new question
                document.querySelector(`#question-${questionIndex}`).style.display = 'block';

                currentQuestion = questionIndex;
                updateNavigationButtons();
            }

            function nextQuestion() {
                if (currentQuestion < totalQuestions - 1) {
                    goToQuestion(currentQuestion + 1);
                }
            }

            function previousQuestion() {
                if (currentQuestion > 0) {
                    goToQuestion(currentQuestion - 1);
                }
            }

            function updateNavigationButtons() {
                const prevBtns = document.querySelectorAll('.btn-previous, #bottomPrevBtn');
                const nextBtns = document.querySelectorAll('.btn-next, #bottomNextBtn');
                prevBtns.forEach(btn => btn.disabled = currentQuestion === 0);
                nextBtns.forEach(btn => btn.disabled = currentQuestion === totalQuestions - 1);

                const currentNumEl = document.getElementById('currentQuestionNum');
                if (currentNumEl) currentNumEl.textContent = currentQuestion + 1;
                const topCounter = document.getElementById('topQuestionCounter');
                if (topCounter) topCounter.textContent = `${currentQuestion + 1} / ${totalQuestions}`;
            }

            // Answer selection
            function selectOption(questionIndex, optionIndex) {
                console.log('selectOption called:', questionIndex, optionIndex);

                const questionCard = document.querySelector(`#question-${questionIndex}`);
                const options = questionCard.querySelectorAll('.option-item');

                console.log('Found question card:', questionCard);
                console.log('Found options:', options.length);

                if (!questionCard || options.length === 0) {
                    console.error('Question card or options not found');
                    return;
                }

                if (optionIndex >= options.length) {
                    console.error('Option index out of range:', optionIndex, 'max:', options.length - 1);
                    return;
                }

                // Clear previous selections
                options.forEach(option => option.classList.remove('selected'));

                // Select current option
                options[optionIndex].classList.add('selected');
                const radioInput = options[optionIndex].querySelector('input');
                if (radioInput) {
                    radioInput.checked = true;
                }

                // Store answer
                answers[questionIndex] = optionIndex;
                console.log('Answer stored:', answers);

                // Mark question as answered in navigator
                const navQuestions = document.querySelectorAll('.nav-question');
                if (navQuestions[questionIndex]) {
                    navQuestions[questionIndex].classList.add('answered');
                }
            }

            // Fill in the blanks
            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('fill-blanks-input')) {
                    const questionIndex = e.target.dataset.question;
                    const blankIndex = e.target.dataset.blank;

                    if (!answers[questionIndex]) {
                        answers[questionIndex] = {};
                    }

                    answers[questionIndex][blankIndex] = e.target.value;

                    // Mark question as answered if any blank is filled
                    if (Object.keys(answers[questionIndex]).length > 0) {
                        document.querySelectorAll('.nav-question')[questionIndex].classList.add('answered');
                    }
                }
            });

            // Submit test
            function submitTest() {
                const answeredCount = Object.keys(answers).length;
                const unansweredCount = totalQuestions - answeredCount;

                let confirmMessage = 'Are you sure you want to submit your test?';
                if (unansweredCount > 0) {
                    confirmMessage += `\n\nYou have ${unansweredCount} unanswered question(s).`;
                }

                if (confirm(confirmMessage)) {
                    // Debug: show the submit route URL in DOM (hidden)
                    const debugEl = document.createElement('div');
                    debugEl.id = 'debug-submit-url';
                    debugEl.style.display = 'none';
                    debugEl.textContent = '{{ route('student.test.submit', $test) }}';
                    document.body.appendChild(debugEl);

                    // Prepare submission data
                    const now = Date.now();
                    const timeTaken = Math.round((now - startTime) / 1000);

                    // Base64 encode the answers payload to bypass any web application firewalls (e.g. ModSecurity on Hostinger)
                    // that might trigger a 403 error on French special characters/apostrophes/quotes in essays or passages.
                    const safeAnswers = btoa(unescape(encodeURIComponent(JSON.stringify(answers))));

                    const submissionData = {
                        test_id: {{ $test->id }},
                        answers: safeAnswers,
                        time_taken: timeTaken,
                        is_encoded: true
                    };

                    console.log('Submitting test with data:', submissionData);

                    // Submit via AJAX
                    const submitUrl = '{{ route('student.test.submit', $test) }}';
                    console.log('Submitting to URL:', submitUrl);
                    fetch(submitUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(submissionData)
                    })
                        .then(async response => {
                            const contentType = response.headers.get('content-type') || '';
                            const isJson = contentType.includes('application/json');
                            let payload = null;
                            if (isJson) {
                                try { payload = await response.json(); } catch (e) { payload = null; }
                            } else {
                                try { const txt = await response.text(); console.error('Non-JSON body snippet:', txt?.slice(0, 500)); } catch (e) { }
                            }
                            console.log('Response status:', response.status, 'Final URL:', response.url, 'Payload:', payload);
                            if (!response.ok) {
                                const serverMsg = payload && typeof payload.message === 'string' ? payload.message : null;
                                let userMsg = serverMsg || `Request failed (${response.status})`;
                                if (response.status === 403 && serverMsg && serverMsg.toLowerCase().includes('maximum attempts')) {
                                    userMsg = 'You have reached the maximum number of attempts for this test.';
                                }
                                alert(userMsg);
                                throw new Error(userMsg);
                            }
                            return payload;
                        })
                        .then(data => {
                            console.log('Response data:', data);
                            if (data.success) {
                                window.location.href = data.redirect_url;
                            } else {
                                alert('Error submitting test: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error submitting test: ' + error.message);
                        });
                }
            }

            // Word Count and Accent Helper Functions
            // Word Count and Accent Helper Functions
            // Use a global object for state to ensure persistence
            window.capsState = window.capsState || {};

            function toggleCaps(questionId) {
                // Force boolean conversion
                window.capsState[questionId] = !window.capsState[questionId];

                updateAccentButtons(questionId);
                updateCapsButtonVisuals(questionId);
            }

            function updateCapsButtonVisuals(questionId) {
                const btn = document.getElementById(`caps-btn-${questionId}`);
                if (!btn) return;

                if (window.capsState[questionId]) {
                    btn.classList.add('active', 'btn-primary', 'text-white');
                    btn.classList.remove('btn-outline-secondary');
                } else {
                    btn.classList.remove('active', 'btn-primary', 'text-white');
                    btn.classList.add('btn-outline-secondary');
                }
            }

            function updateAccentButtons(questionId) {
                const container = document.getElementById(`accent-buttons-${questionId}`);
                if (!container) return;

                const buttons = container.querySelectorAll('.accent-btn');
                const isCaps = window.capsState[questionId];

                buttons.forEach(btn => {
                    // Ensure we have the original char
                    if (!btn.dataset.originalChar) {
                        btn.dataset.originalChar = btn.dataset.char || btn.textContent.trim();
                    }

                    const char = btn.dataset.originalChar;
                    const displayChar = isCaps ? char.toUpperCase() : char;
                    btn.textContent = displayChar;
                });
            }

            function insertCharacter(btnOrChar, inputId) {
                let char = '';

                if (typeof btnOrChar === 'object' && btnOrChar.dataset) {
                    // It's a button element
                    // Use dataset.originalChar if available (set by our update function), otherwise dataset.char
                    char = btnOrChar.dataset.originalChar || btnOrChar.dataset.char;

                    // Check caps state
                    const questionId = inputId.replace('answer-', '');
                    if (window.capsState[questionId]) {
                        char = char.toUpperCase();
                    }
                } else {
                    // Direct character string passed
                    char = String(btnOrChar);
                }

                const textarea = document.getElementById(inputId);
                if (!textarea) return;

                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;

                textarea.value = text.substring(0, start) + char + text.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + 1;
                textarea.focus();
                textarea.dispatchEvent(new Event('input'));

                // Also trigger word count update immediately
                const min = textarea.getAttribute('data-min-words') || 0;
                const max = textarea.getAttribute('data-max-words') || 0;
                if (typeof updateWordCount === 'function') {
                    updateWordCount(textarea, min, max);
                }
            }

            // Sync logic
            function syncCapsState(isSystemCaps) {
                document.querySelectorAll('[id^="caps-btn-"]').forEach(btn => {
                    const qId = btn.id.replace('caps-btn-', '');
                    if (window.capsState[qId] !== isSystemCaps) {
                        window.capsState[qId] = isSystemCaps;
                        updateAccentButtons(qId);
                        updateCapsButtonVisuals(qId);
                    }
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.getModifierState && event.key === 'CapsLock') {
                    const isCaps = event.getModifierState("CapsLock");
                    syncCapsState(isCaps);
                }
            });

            document.addEventListener('keyup', (e) => {
                if (e.getModifierState) syncCapsState(e.getModifierState("CapsLock"));
            });

            document.addEventListener('click', (e) => {
                if (e.getModifierState) syncCapsState(e.getModifierState("CapsLock"));
            });

            function updateWordCount(textarea, min, max) {
                const text = textarea.value.trim();
                const wordCount = text ? text.split(/\s+/).length : 0;
                const counterEl = document.getElementById(textarea.id.replace('answer-', 'word-count-'));

                if (counterEl) {
                    counterEl.textContent = `${wordCount} words`;
                    if (min > 0 && wordCount < min) {
                        counterEl.classList.add('text-warning');
                        counterEl.classList.remove('text-success', 'text-danger');
                    } else if (max > 0 && wordCount > max) {
                        counterEl.classList.add('text-danger');
                        counterEl.classList.remove('text-success', 'text-warning');
                    } else {
                        counterEl.classList.add('text-success');
                        counterEl.classList.remove('text-warning', 'text-danger');
                    }
                }

                const questionId = textarea.id.replace('answer-', '');
                const questionIndex = document.querySelector(`.question-card [id="answer-${questionId}"]`).closest('.question-card').id.replace('question-', '');

                // For standard test submission structure
                if (!answers[questionIndex]) {
                    answers[questionIndex] = text;
                } else {
                    answers[questionIndex] = text;
                }

                // Also update global answers object keyed by question ID if needed, 
                // but test.blade.php uses question INDEX for answers object currently?
                // Wait, selectOption uses `answers[questionIndex] = optionIndex`.
                // So I should stick to questionIndex.

                // Mark as answered
                if (text.length > 0) {
                    const navQuestions = document.querySelectorAll('.nav-question');
                    if (navQuestions[questionIndex]) {
                        navQuestions[questionIndex].classList.add('answered');
                    }
                }
            }

            // Prevent accidental page refresh
            window.addEventListener('beforeunload', function (e) {
                if (Object.keys(answers).length > 0) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Debug: Initialize test debugging

            // Pre-test modal handler
            function startTest() {
                document.getElementById('instructionsModal').style.display = 'none';
            }

            document.addEventListener('DOMContentLoaded', function () {
                console.log('=== TEST DEBUG INFO ===');
                console.log('Total questions: {{ $totalQuestions }}');

                @foreach($test->questions->sortBy('order') as $index => $question)
                    console.log('Question {{ $index }}: Type={{ $question->type }}, Options={{ $question->options->count() }}');
                @endforeach

                console.log('=== END DEBUG INFO ===');
            });
        </script>
    @endpush
@endsection