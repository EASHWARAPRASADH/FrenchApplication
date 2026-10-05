@extends('layouts.app')

@section('title', 'Test Preview - ' . $test->title)

@section('content')
    <style>
        /* Test Preview Styles */
        .test-preview-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .test-header {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .test-title {
            color: #2d3748;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .test-description {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .test-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .info-item {
            text-align: center;
        }

        .info-value {
            display: block;
            font-size: 1.5rem;
            font-weight: 600;
            color: #667eea;
        }

        .info-label {
            font-size: 0.9rem;
            color: #718096;
            margin-top: 0.25rem;
        }

        .preview-notice {
            background: #e6fffa;
            border: 1px solid #81e6d9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .preview-notice i {
            color: #319795;
            margin-right: 0.5rem;
        }

        .question-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .question-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .question-number {
            background: #667eea;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
        }

        .question-type-badge {
            background: #e2e8f0;
            color: #4a5568;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: auto;
        }

        .question-text {
            font-size: 1.1rem;
            color: #2d3748;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .question-media {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .question-media img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .question-media video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .options-list {
            list-style: none;
            padding: 0;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .option-item:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
        }

        .option-item.correct {
            background: #f0fff4;
            border-color: #68d391;
        }

        .option-radio {
            margin-right: 1rem;
            transform: scale(1.2);
        }

        .option-text {
            flex: 1;
            color: #2d3748;
        }

        .correct-indicator {
            color: #38a169;
            font-weight: 600;
            margin-left: 1rem;
        }

        .fill-blanks-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #2d3748;
        }

        .blank-input {
            display: inline-block;
            min-width: 100px;
            padding: 0.25rem 0.5rem;
            border: none;
            border-bottom: 2px solid #667eea;
            background: transparent;
            font-size: inherit;
            text-align: center;
            margin: 0 0.25rem;
        }

        .blank-input:focus {
            outline: none;
            border-bottom-color: #4c51bf;
        }

        @media (max-width: 768px) {
            .test-preview-container {
                padding: 1rem;
            }

            .test-info {
                gap: 1rem;
            }

            .question-card {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="test-preview-container">
        <!-- Preview Notice -->
        <div class="preview-notice">
            <i class="bi bi-eye"></i>
            <strong>Preview Mode:</strong> This is how students will see this test. No answers will be saved.
        </div>

        <!-- Test Header -->
        <div class="test-header">
            <h1 class="test-title">{{ $test->title }}</h1>
            @if($test->description)
                <p class="test-description">{{ $test->description }}</p>
            @endif

            <div class="test-info">
                <div class="info-item">
                    <span class="info-value">{{ $test->questions->count() }}</span>
                    <div class="info-label">Questions</div>
                </div>
                @if($test->time_limit)
                    <div class="info-item">
                        <span class="info-value">{{ $test->time_limit }}</span>
                        <div class="info-label">Minutes</div>
                    </div>
                @endif
                <div class="info-item">
                    <span class="info-value">{{ $test->passing_score }}%</span>
                    <div class="info-label">Passing Score</div>
                </div>
                @if($test->max_attempts)
                    <div class="info-item">
                        <span class="info-value">{{ $test->max_attempts }}</span>
                        <div class="info-label">Max Attempts</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Questions -->
        @foreach($test->questions as $index => $question)
            <div class="question-card">
                <div class="question-header">
                    <div class="question-number">{{ $index + 1 }}</div>
                    <div class="question-type-badge">
                        {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                    </div>
                </div>

                <div class="question-text">{{ $question->question_text }}</div>

                @if(in_array($question->type, ['passage_mcq', 'expression_ecrite']) && $question->passage)
                    <div class="question-passage">
                        <div class="passage-box p-3 mb-3 border rounded bg-light">
                            @if($question->type === 'expression_ecrite')
                                @php
                                    // Parse expression ecrite passage into documents
                                    $passageText = $question->passage;
                                    $lines = explode("\n", $passageText);
                                    $intro = '';
                                    $documents = [];
                                    $currentDoc = null;

                                    foreach ($lines as $line) {
                                        $line = trim($line);
                                        if (empty($line))
                                            continue;

                                        // Check if line is a document header
                                        if (preg_match('/^(Document\s+\d+)\s*:(.*)$/i', $line, $matches)) {
                                            if ($currentDoc) {
                                                $documents[] = $currentDoc;
                                            }
                                            $currentDoc = [
                                                'title' => trim($matches[1]),
                                                'content' => trim($matches[2])
                                            ];
                                        } elseif ($currentDoc) {
                                            $currentDoc['content'] .= ' ' . $line;
                                        } else {
                                            $intro .= ' ' . $line;
                                        }
                                    }

                                    if ($currentDoc) {
                                        $documents[] = $currentDoc;
                                    }
                                @endphp

                                @if($intro)
                                    <div class="mb-3">
                                        <strong class="text-primary">Instructions:</strong>
                                        <p class="mt-2 mb-0">{{ trim($intro) }}</p>
                                    </div>
                                @endif

                                @foreach($documents as $doc)
                                    <div class="mb-3">
                                        <strong class="text-info">{{ $doc['title'] }}</strong>
                                        <p class="mt-1 mb-0">{{ trim($doc['content']) }}</p>
                                    </div>
                                @endforeach
                            @else
                                {!! nl2br(e($question->passage)) !!}
                            @endif
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
                                <img src="{{ $imageSrc }}" alt="Question Image" class="mb-3">
                            @endif
                            @if($audioSrc)
                                <div class="audio-container">
                                    <i class="bi bi-volume-up fs-3 text-warning mb-2"></i>
                                    <audio controls class="w-100">
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
                                <audio controls class="w-100">
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
                            <img src="{{ $mediaSrc }}" alt="Question Image">
                        @endif
                    </div>
                @endif

                <!-- Question Options -->
                @if(in_array($question->type, ['mcq', 'mcq_image', 'audio', 'video', 'image_mcq', 'passage_mcq', 'image_audio_mcq']))
                    <ul class="options-list">
                        @foreach($question->options->sortBy('order')->values() as $option)
                            <li class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                <input type="radio" class="option-radio" name="question_{{ $question->id }}" disabled>
                                @if(!empty($option->option_image))
                                    <img src="{{ preg_match('/^https?:\\/\\//', $option->option_image ?? '') ? $option->option_image : asset($option->option_image) }}"
                                        alt="Option Image" class="option-image me-2" style="max-height: 80px;">
                                @endif
                                <span class="option-text">{{ $option->option_text }}</span>
                                @if($option->is_correct)
                                    <span class="correct-indicator"><i class="bi bi-check-lg me-1"></i>Correct</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @elseif($question->type === 'fill_blanks')
                    <div class="fill-blanks-text">
                        {!! preg_replace('/\[blank\]/', '<input type="text" class="blank-input" disabled>', $question->question_text) !!}
                    </div>
                    @if($question->correct_answer && is_array($question->correct_answer))
                        <div class="mt-3">
                            <strong>Correct answers:</strong> {{ implode(', ', $question->correct_answer) }}
                        </div>
                    @endif
                @endif
            </div>
        @endforeach

        @if($test->questions->count() === 0)
            <div class="question-card text-center">
                <i class="bi bi-question-circle" style="font-size: 3rem; color: #cbd5e0;"></i>
                <h3 class="mt-3 text-muted">No Questions Yet</h3>
                <p class="text-muted">Add questions to this test to see the preview.</p>
            </div>
        @endif
    </div>
@endsection