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
            showOfficeViewer('PowerPoint presentation', true);
        } else if (mimeType.includes('excel') || mimeType.includes('spreadsheet') || mimeType.includes('csv')) {
            showOfficeViewer('Excel spreadsheet', false);
        } else if (mimeType.includes('word') || mimeType.includes('document')) {
            showOfficeViewer('Word document', false);
        } else if (mimeType.includes('pdf')) {
            // For PDF files, embed directly with disabled toolbar
            container.innerHTML = `
                <div class="text-center p-3 position-relative">
                    @if($accessLevel !== 'download')
                    <div style="position: absolute; top: 1rem; left: 1rem; right: 1rem; height: 55px; background: transparent; z-index: 10; cursor: not-allowed;" title="Download tools disabled"></div>
                    @endif
                    <p class="text-muted mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Viewing PDF document
                    </p>
                    <iframe
                        src="${fileUrl}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                        style="width: 100%; height: 750px; border: none; border-radius: 0.375rem;"
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
                    <img src="${fileUrl}" class="img-fluid" style="max-height: 650px; border-radius: 0.375rem;" alt="${fileName}">
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
                        This file is view-only but cannot be previewed directly in the browser.
                    @endif
                </div>
            `;
        }
    }

    function showOfficeViewer(docTypeName, isPresentation = false) {
        container.innerHTML = `
            <div class="text-center p-3 position-relative" oncontextmenu="return false;">
                @if($accessLevel !== 'download')
                <div style="position: absolute; top: 1rem; left: 1rem; right: 1rem; height: 55px; background: transparent; z-index: 10; cursor: not-allowed;" title="Download tools disabled"></div>
                @endif
                <p class="text-muted mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Viewing ${docTypeName}
                </p>

                <!-- Viewer Selection Tabs -->
                <ul class="nav nav-tabs justify-content-center mb-3" id="viewerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="office-tab" data-bs-toggle="tab" data-bs-target="#office-viewer"
                                type="button" role="tab" aria-controls="office-viewer" aria-selected="true">
                            <i class="bi bi-microsoft me-1"></i>Office Online (Fast)
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
                        <iframe
                            src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}"
                            style="width: 100%; height: 750px; border: none; border-radius: 0.375rem;"
                            allowfullscreen>
                        </iframe>
                        ${isPresentation ? `
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="bi bi-volume-up me-1"></i>
                                Audio, animations, and transitions are supported in Office Online viewer.
                            </small>
                        </div>` : ''}
                    </div>

                    <!-- Google Docs Viewer -->
                    <div class="tab-pane fade" id="google-viewer" role="tabpanel" aria-labelledby="google-tab">
                        <iframe
                            src="https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true&cb=${Date.now()}"
                            style="width: 100%; height: 750px; border: none; border-radius: 0.375rem;"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        `;

        // Handle iframe loading errors for Office Online
        setTimeout(() => {
            const officeIframe = document.querySelector('#office-viewer iframe');
            if (officeIframe) {
                officeIframe.onerror = function() {
                    const googleTab = document.querySelector('#google-tab');
                    if (googleTab) googleTab.click();
                };
            }
        }, 1500);
    }

    // Keyboard shortcut protection against saving/printing if not downloadable
    window.addEventListener('keydown', function(e) {
        @if($accessLevel !== 'download')
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) {
            e.preventDefault();
            return false;
        }
        @endif
    });
});
</script>
@endpush
@endsection
