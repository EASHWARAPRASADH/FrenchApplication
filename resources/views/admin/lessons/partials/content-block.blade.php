<div class="content-block" data-block-id="{{ $block->id }}" data-block-type="{{ $block->type }}">
    <div class="block-header">
        <div class="d-flex align-items-center">
            <div class="block-type-icon block-type-{{ $block->type }}">
                @switch($block->type)
                    @case('text')
                        <i class="bi bi-type text-primary"></i>
                        @break
                    @case('image')
                        <i class="bi bi-image text-success"></i>
                        @break
                    @case('video')
                        <i class="bi bi-play-circle text-danger"></i>
                        @break
                    @case('audio')
                        <i class="bi bi-music-note text-info"></i>
                        @break
                    @case('exercise')
                        <i class="bi bi-puzzle text-warning"></i>
                        @break
                    @case('document')
                        @php
                            $mimeType = $block->content['mime_type'] ?? '';
                            if (str_contains($mimeType, 'pdf')) {
                                $icon = 'bi-file-earmark-pdf text-danger';
                            } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                                $icon = 'bi-file-earmark-word text-primary';
                            } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) {
                                $icon = 'bi-file-earmark-ppt text-warning';
                            } else {
                                $icon = 'bi-file-earmark text-secondary';
                            }
                        @endphp
                        <i class="bi {{ $icon }}"></i>
                        @break
                @endswitch
            </div>
            <div>
                <h6 class="mb-0">{{ ucfirst($block->type) }} Block</h6>
                <small class="text-muted">Block #{{ $block->order }}</small>
            </div>
        </div>
        <div class="block-actions">
            <span class="drag-handle" title="Drag to reorder">
                <i class="bi bi-grip-vertical"></i>
            </span>
            <button class="btn btn-sm btn-outline-primary" onclick="editBlock({{ $block->id }})" title="Edit">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteBlock({{ $block->id }})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
    <div class="block-content">
        @switch($block->type)
            @case('text')
                <div class="text-content" style="text-align: {{ $block->content['alignment'] ?? 'left' }}; font-size: {{ $block->content['size'] === 'small' ? '0.9em' : ($block->content['size'] === 'large' ? '1.2em' : ($block->content['size'] === 'extra-large' ? '1.4em' : '1em')) }};">
                    @if(!empty($block->content['highlighted']))
                        <div class="alert alert-info">
                            {!! $block->content['html'] ?? 'No content' !!}
                        </div>
                    @else
                        {!! $block->content['html'] ?? 'No content' !!}
                    @endif
                </div>
                @break

            @case('image')
                <div class="image-content text-{{ $block->content['alignment'] ?? 'center' }}">
                    @if(isset($block->content['url']))
                        <img src="{{ $block->content['url'] }}"
                             alt="{{ $block->content['alt'] ?? '' }}"
                             class="img-fluid @if($block->content['rounding'] === 'rounded') rounded @elseif($block->content['rounding'] === 'circle') rounded-circle @endif"
                             style="width: @switch($block->content['size'] ?? 'large') @case('small') 25% @break @case('medium') 50% @break @case('large') 75% @break @case('full') 100% @break @endswitch;"
                             @if(!empty($block->content['lightbox'])) data-bs-toggle="modal" data-bs-target="#imageModal" @endif>
                        @if(!empty($block->content['caption']))
                            <p class="image-caption text-muted mt-2">{{ $block->content['caption'] }}</p>
                        @endif
                    @else
                        <p class="text-muted">No image uploaded</p>
                    @endif
                </div>
                @break

            @case('video')
                <div class="video-content text-{{ $block->content['alignment'] ?? 'center' }}">
                    @if(isset($block->content['url']))
                        <div class="ratio ratio-16x9" style="width: @switch($block->content['size'] ?? 'large') @case('small') 50% @break @case('medium') 75% @break @case('large') 100% @break @endswitch; margin: 0 auto;">
                            @if($block->content['source'] === 'upload')
                                <video controls @if(!empty($block->content['autoplay'])) autoplay @endif @if(empty($block->content['controls'])) style="pointer-events: none;" @endif>
                                    <source src="{{ $block->content['url'] }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <iframe src="{{ $block->content['url'] }}@if(!empty($block->content['autoplay']))?autoplay=1@endif" allowfullscreen></iframe>
                            @endif
                        </div>
                        @if(!empty($block->content['caption']))
                            <p class="video-caption text-muted mt-2">{{ $block->content['caption'] }}</p>
                        @endif
                    @else
                        <p class="text-muted">No video added</p>
                    @endif
                </div>
                @break

            @case('audio')
                <div class="audio-content text-{{ $block->content['alignment'] ?? 'center' }}">
                    @if(isset($block->content['url']))
                        @if(!empty($block->content['title']))
                            <h6 class="audio-title">{{ $block->content['title'] }}</h6>
                        @endif
                        <audio controls class="w-100" @if(!empty($block->content['autoplay'])) autoplay @endif @if(!empty($block->content['loop'])) loop @endif>
                            <source src="{{ $block->content['url'] }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        @if(!empty($block->content['caption']))
                            <p class="audio-caption text-muted mt-2">{{ $block->content['caption'] }}</p>
                        @endif
                        @if(!empty($block->content['transcript']))
                            <details class="mt-2">
                                <summary class="text-muted">Transcript</summary>
                                <div class="transcript-content mt-2 p-2 bg-light rounded">
                                    {{ $block->content['transcript'] }}
                                </div>
                            </details>
                        @endif
                        @if(!empty($block->content['downloadable']))
                            <div class="mt-2">
                                <a href="{{ $block->content['url'] }}" download class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download me-1"></i>
                                    Download Audio
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No audio file added</p>
                    @endif
                </div>
                @break

            @case('document')
                <div class="document-content text-{{ $block->content['alignment'] ?? 'left' }}">
                    @if(isset($block->content['url']))
                        @php
                            $mimeType = $block->content['mime_type'] ?? '';
                            if (str_contains($mimeType, 'pdf')) {
                                $icon = 'bi-file-earmark-pdf text-danger';
                                $docType = 'PDF';
                            } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                                $icon = 'bi-file-earmark-word text-primary';
                                $docType = 'Word Document';
                            } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) {
                                $icon = 'bi-file-earmark-ppt text-warning';
                                $docType = 'PowerPoint';
                            } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'csv')) {
                                $icon = 'bi-file-earmark-spreadsheet text-success';
                                $docType = 'Excel Spreadsheet';
                            } else {
                                $icon = 'bi-file-earmark text-secondary';
                                $docType = 'Document';
                            }
                        @endphp
                        <div class="document-card p-3 border rounded" style="background: #f8f9fa; display: inline-block;">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <i class="bi {{ $icon }} fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $block->content['title'] ?? 'Document' }}</h6>
                                    @if(!empty($block->content['description']))
                                        <p class="text-muted small mb-2">{{ $block->content['description'] }}</p>
                                    @endif
                                    <small class="text-muted">
                                        {{ $docType }} • {{ number_format(($block->content['size'] ?? 0) / 1024, 2) }} KB
                                    </small>
                                    <div class="mt-2 d-flex gap-2">
                                        @if(!empty($block->content['previewable']))
                                            @if(str_contains($mimeType, 'pdf'))
                                                <a href="{{ $block->content['url'] }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye me-1"></i>Preview
                                                </a>
                                            @elseif(str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation') || str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'word') || str_contains($mimeType, 'document'))
                                                <a href="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($block->content['url']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye me-1"></i>Preview
                                                </a>
                                            @endif
                                        @endif
                                        @if(!empty($block->content['downloadable']))
                                            <a href="{{ $block->content['url'] }}" download class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No document uploaded</p>
                    @endif
                </div>
                @break

            @case('exercise')
                <div class="exercise-content">
                    @if(isset($block->content['question']))
                        <div class="exercise-header d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">{{ $block->content['question'] }}</h6>
                            <span class="badge bg-warning text-dark">{{ $block->content['points'] ?? 1 }} point(s)</span>
                        </div>

                        @switch($block->content['type'] ?? 'multiple-choice')
                            @case('multiple-choice')
                                <div class="multiple-choice-exercise">
                                    @foreach($block->content['options'] ?? [] as $index => $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="exercise_{{ $block->id }}" id="option_{{ $block->id }}_{{ $index }}" value="{{ $index }}" disabled>
                                            <label class="form-check-label" for="option_{{ $block->id }}_{{ $index }}">
                                                {{ is_array($option) ? $option['text'] : $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @break

                            @case('fill-blanks')
                                <div class="fill-blanks-exercise">
                                    <p class="exercise-sentence">
                                        {!! str_replace('[blank]', '<input type="text" class="form-control d-inline-block" style="width: 100px; margin: 0 5px;" disabled>', $block->content['sentence'] ?? '') !!}
                                    </p>
                                </div>
                                @break

                            @case('matching')
                                <div class="matching-exercise">
                                    <div class="row">
                                        <div class="col-6">
                                            <h6>Match these items:</h6>
                                            @foreach($block->content['pairs'] ?? [] as $index => $pair)
                                                <div class="matching-item mb-2 p-2 border rounded">
                                                    {{ $pair['left'] }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-6">
                                            <h6>With these:</h6>
                                            @foreach(collect($block->content['pairs'] ?? [])->shuffle() as $index => $pair)
                                                <div class="matching-target mb-2 p-2 border rounded bg-light">
                                                    {{ $pair['right'] }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @break
                        @endswitch

                        @if(!empty($block->content['explanation']))
                            <div class="exercise-explanation mt-3 p-2 bg-info bg-opacity-10 rounded">
                                <small class="text-muted">
                                    <strong>Explanation:</strong> {{ $block->content['explanation'] }}
                                </small>
                            </div>
                        @endif

                        @if(!empty($block->content['required']))
                            <div class="mt-2">
                                <small class="text-danger">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    This exercise is required to complete the lesson
                                </small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No exercise configured</p>
                    @endif
                </div>
                @break
        @endswitch
    </div>
</div>

<style>
.content-block {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    overflow: hidden;
}

.content-block:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #7b1fa2;
}

.content-block.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.block-header {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.block-type-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    font-size: 1.2rem;
}

.block-type-text {
    background: #e3f2fd;
}

.block-type-image {
    background: #e8f5e8;
}

.block-type-video {
    background: #ffebee;
}

.block-type-audio {
    background: #e1f5fe;
}

.block-type-exercise {
    background: #fff3e0;
}

.block-type-document {
    background: #f3e5f5;
}

.block-actions {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.block-content {
    padding: 1.5rem;
}

.drag-handle {
    cursor: grab;
    padding: 0.25rem;
    margin-right: 0.5rem;
    color: #6c757d;
    font-size: 1.1rem;
}

.drag-handle:hover {
    color: #495057;
}

.drag-handle:active {
    cursor: grabbing;
}

.exercise-content .form-check-input:checked + .form-check-label {
    font-weight: 500;
    color: #198754;
}

.matching-item, .matching-target {
    cursor: pointer;
    transition: all 0.3s ease;
}

.matching-item:hover, .matching-target:hover {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
}

.exercise-header .badge {
    font-size: 0.75rem;
}

.transcript-content {
    font-size: 0.9rem;
    line-height: 1.5;
}

.audio-title {
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
