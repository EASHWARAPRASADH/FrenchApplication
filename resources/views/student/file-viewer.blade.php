@extends('layouts.app')

@section('title', 'View File - ' . $file->original_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="{{ $file->icon }} me-2"></i>{{ $file->original_name }}
                    </h4>
                    <small class="text-muted">
                        {{ $file->type }} • {{ number_format($file->size / 1024, 2) }} KB
                        @if($accessLevel === 'view')
                            • <span class="badge bg-info">View Only</span>
                        @elseif($accessLevel === 'download')
                            • <span class="badge bg-success">Download Allowed</span>
                        @endif
                    </small>
                </div>
                <div>
                    @if($accessLevel === 'download')
                        <a href="{{ route('student.file.download', $file) }}" class="btn btn-success me-2">
                            <i class="bi bi-download me-1"></i>Download
                        </a>
                    @endif
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </button>
                </div>
            </div>
        </div>

        <!-- File Viewer -->
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div id="file-viewer-container" style="min-height: 600px;">
                        <!-- Loading indicator -->
                        <div class="text-center py-5" id="loading-indicator">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading file viewer...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('file-viewer-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    const fileUrl = @json($fileUrl);
    const mimeType = @json($file->mime_type);
    const fileName = @json($file->original_name);

    // Hide loading indicator after a short delay
    setTimeout(() => {
        loadingIndicator.style.display = 'none';
        showFileViewer();
    }, 500);

    function showFileViewer() {
        // Check file type and display accordingly
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) {
            // For PPTX files, try multiple viewers for better audio support
            showPowerPointViewer();
        } else if (mimeType.includes('pdf')) {
            // For PDF files, embed directly
            container.innerHTML = `
                <div class="text-center p-3">
                    <p class="text-muted mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Viewing PDF document
                    </p>
                    <iframe
                        src="${fileUrl}"
                        style="width: 100%; height: 600px; border: none; border-radius: 0.375rem;"
                        allowfullscreen>
                    </iframe>
                </div>
            `;
        } else if (mimeType.includes('word') || mimeType.includes('document')) {
            // For Word documents, use Google Docs Viewer
            container.innerHTML = `
                <div class="text-center p-3">
                    <p class="text-muted mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Viewing Word document
                    </p>
                    <iframe
                        src="https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true"
                        style="width: 100%; height: 600px; border: none; border-radius: 0.375rem;"
                        allowfullscreen>
                    </iframe>
                </div>
            `;
        } else if (mimeType.includes('image')) {
            // For images, display directly
            container.innerHTML = `
                <div class="text-center p-3">
                    <p class="text-muted mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Viewing image
                    </p>
                    <img src="${fileUrl}" class="img-fluid" style="max-height: 600px; border-radius: 0.375rem;" alt="${fileName}">
                </div>
            `;
        } else {
            // For unsupported file types
            container.innerHTML = `
                <div class="alert alert-info m-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Preview not available for this file type.
                    @if($accessLevel === 'download')
                        Please download the file to view its contents.
                    @else
                        This file is view-only but cannot be previewed in the browser.
                    @endif
                </div>
            `;
        }
    }

    function showPowerPointViewer() {
        // Create viewer options for PowerPoint with audio support
        container.innerHTML = `
            <div class="text-center p-3">
                <p class="text-muted mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Viewing PowerPoint presentation
                </p>

                <!-- Viewer Selection Tabs -->
                <ul class="nav nav-tabs justify-content-center mb-3" id="viewerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="office-tab" data-bs-toggle="tab" data-bs-target="#office-viewer"
                                type="button" role="tab" aria-controls="office-viewer" aria-selected="true">
                            <i class="bi bi-microsoft me-1"></i>Office Online
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="google-tab" data-bs-toggle="tab" data-bs-target="#google-viewer"
                                type="button" role="tab" aria-controls="google-viewer" aria-selected="false">
                            <i class="bi bi-google me-1"></i>Google Viewer
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="viewerTabContent">
                    <!-- Office Online Viewer -->
                    <div class="tab-pane fade show active" id="office-viewer" role="tabpanel" aria-labelledby="office-tab">
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Enhanced Viewer:</strong> This viewer supports audio playback and interactive features.
                            </div>
                        </div>
                        <iframe
                            src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}"
                            style="width: 100%; height: 600px; border: none; border-radius: 0.375rem;"
                            allowfullscreen>
                        </iframe>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="bi bi-volume-up me-1"></i>
                                Audio and animations are supported in this viewer.
                            </small>
                        </div>
                    </div>

                    <!-- Google Docs Viewer -->
                    <div class="tab-pane fade" id="google-viewer" role="tabpanel" aria-labelledby="google-tab">
                        <div class="mb-3">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Basic Viewer:</strong> Audio and animations may not work in this viewer.
                            </div>
                        </div>
                        <iframe
                            src="https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true"
                            style="width: 100%; height: 600px; border: none; border-radius: 0.375rem;"
                            allowfullscreen>
                        </iframe>
                        <div class="mt-2">
                            <small class="text-warning">
                                <i class="bi bi-volume-mute me-1"></i>
                                Switch to Office Online viewer above for audio support.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center py-2">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <strong>Office Online:</strong> Full audio support
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body text-center py-2">
                                    <small class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Google Viewer:</strong> Limited features
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Handle iframe loading errors for Office Online
        setTimeout(() => {
            const officeIframe = document.querySelector('#office-viewer iframe');
            if (officeIframe) {
                officeIframe.onerror = function() {
                    // If Office Online fails, show a message and switch to Google viewer
                    document.querySelector('#google-tab').click();
                    const alertDiv = document.querySelector('#office-viewer .alert');
                    if (alertDiv) {
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.innerHTML = `
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Office Online Unavailable:</strong> Switched to Google Viewer. Audio may not work.
                        `;
                    }
                };
            }
        }, 1000);
    }
});
</script>
@endpush
@endsection
