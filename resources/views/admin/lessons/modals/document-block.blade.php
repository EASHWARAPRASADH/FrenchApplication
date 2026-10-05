<!-- Document Block Modal -->
<div class="modal fade" id="documentBlockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                    Add Document Block
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="documentBlockForm">
                    <!-- Document Title -->
                    <div class="mb-3">
                        <label class="form-label">Document Title</label>
                        <input type="text" class="form-control" id="documentTitle" placeholder="e.g., French Grammar Guide" required>
                        <small class="text-muted">This title will be displayed to students</small>
                    </div>

                    <!-- Document Description -->
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="documentDescription" rows="2" placeholder="Brief description of the document"></textarea>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label">Upload Document</label>
                        <div class="upload-area border-2 border-dashed rounded p-4 text-center" id="documentUploadArea" style="position: relative; cursor: pointer; transition: all 0.3s;">
                            <i class="bi bi-cloud-arrow-up fs-1 text-muted mb-2"></i>
                            <p class="mb-1"><strong>Click to upload or drag and drop</strong></p>
                            <small class="text-muted">PDF, Word (.doc, .docx), PowerPoint (.ppt, .pptx), Excel (.xls, .xlsx, .csv) - Max 100MB</small>
                            <input type="file" id="documentFile" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv" style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer;" required onchange="handleDocumentInputChange(event)">
                        </div>
                        <div id="documentFileInfo" class="mt-2 d-none">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="documentFileName"></span>
                                <small class="d-block text-muted mt-1">Size: <span id="documentFileSize"></span></small>
                            </div>
                        </div>
                    </div>

                    <!-- Display Options -->
                    <div class="mb-3">
                        <label class="form-label">Display Options</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="documentDownloadable">
                            <label class="form-check-label" for="documentDownloadable">
                                Allow students to download
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="documentPreviewable" checked>
                            <label class="form-check-label" for="documentPreviewable">
                                Show preview button (for PDF)
                            </label>
                        </div>
                    </div>

                    <!-- Alignment -->
                    <div class="mb-3">
                        <label class="form-label">Alignment</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="documentAlignment" id="alignLeft" value="left" checked>
                            <label class="btn btn-outline-secondary" for="alignLeft">
                                <i class="bi bi-text-left"></i> Left
                            </label>
                            <input type="radio" class="btn-check" name="documentAlignment" id="alignCenter" value="center">
                            <label class="btn btn-outline-secondary" for="alignCenter">
                                <i class="bi bi-text-center"></i> Center
                            </label>
                            <input type="radio" class="btn-check" name="documentAlignment" id="alignRight" value="right">
                            <label class="btn btn-outline-secondary" for="alignRight">
                                <i class="bi bi-text-right"></i> Right
                            </label>
                        </div>
                    </div>

                    <!-- Upload Progress -->
                    <div id="documentUploadProgress" class="d-none mb-3">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="documentProgressBar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">Uploading... <span id="documentProgressText">0%</span></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDocumentBlockBtn" onclick="saveDocumentBlock()">
                    <i class="bi bi-check-circle me-2"></i>Add Document Block
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Document upload area drag and drop
const documentUploadArea = document.getElementById('documentUploadArea');
const documentFile = document.getElementById('documentFile');
let selectedDocumentFile = null;

if (documentUploadArea) {
    documentUploadArea.addEventListener('click', () => documentFile.click());

    documentUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        documentUploadArea.style.backgroundColor = '#f0f0f0';
        documentUploadArea.style.borderColor = '#007bff';
    });

    documentUploadArea.addEventListener('dragleave', () => {
        documentUploadArea.style.backgroundColor = 'transparent';
        documentUploadArea.style.borderColor = '#dee2e6';
    });

    documentUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        documentUploadArea.style.backgroundColor = 'transparent';
        documentUploadArea.style.borderColor = '#dee2e6';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            selectedDocumentFile = files[0];
            updateDocumentFileInfo();
        }
    });

    documentFile.addEventListener('change', () => { selectedDocumentFile = documentFile.files[0] || null; updateDocumentFileInfo(); });
}

function updateDocumentFileInfo() {
    const file = selectedDocumentFile || documentFile.files[0];
    if (file) {
        const fileInfo = document.getElementById('documentFileInfo');
        const fileName = document.getElementById('documentFileName');
        const fileSize = document.getElementById('documentFileSize');
        
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('d-none');
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function handleDocumentInputChange(e) {
    selectedDocumentFile = (e && e.target && e.target.files && e.target.files[0]) ? e.target.files[0] : null;
    updateDocumentFileInfo();
}

function saveDocumentBlock() {
    const file = selectedDocumentFile || (documentFile.files ? documentFile.files[0] : null);
    if (!file) {
        alert('Please select a document file');
        return;
    }

    const title = document.getElementById('documentTitle').value;
    if (!title) {
        alert('Please enter a document title');
        return;
    }

    uploadDocumentFile(file, title);
}

function uploadDocumentFile(file, title) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', 'document');

    const progressDiv = document.getElementById('documentUploadProgress');
    const progressBar = document.getElementById('documentProgressBar');
    const progressText = document.getElementById('documentProgressText');
    const saveBtn = document.getElementById('saveDocumentBlockBtn');

    progressDiv.classList.remove('d-none');
    saveBtn.disabled = true;

    const xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = percentComplete + '%';
            progressText.textContent = Math.round(percentComplete) + '%';
        }
    });

    xhr.addEventListener('load', () => {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                createDocumentBlock(response, title);
                bootstrap.Modal.getInstance(document.getElementById('documentBlockModal')).hide();
                resetDocumentForm();
            } else {
                alert('Error: ' + response.message);
            }
        } else {
            let msg = 'Upload failed (HTTP ' + xhr.status + ')';
            try {
                const err = JSON.parse(xhr.responseText);
                if (err && err.message) msg = err.message;
            } catch (e) {
                if (xhr.responseText) msg += ': ' + xhr.responseText.substring(0, 200);
            }
            alert(msg);
        }
        progressDiv.classList.add('d-none');
        saveBtn.disabled = false;
    });

    xhr.addEventListener('error', () => {
        alert('Upload error');
        progressDiv.classList.add('d-none');
        saveBtn.disabled = false;
    });

    xhr.open('POST', '/admin/files/upload-content');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);
}

function createDocumentBlock(fileData, title) {
    const description = document.getElementById('documentDescription').value;
    const alignment = document.querySelector('input[name="documentAlignment"]:checked').value;
    const downloadable = document.getElementById('documentDownloadable').checked;
    const previewable = document.getElementById('documentPreviewable').checked;

    const content = {
        title: title,
        description: description,
        url: fileData.url,
        path: fileData.path,
        filename: fileData.filename,
        original_name: fileData.original_name,
        mime_type: fileData.mime_type,
        size: fileData.size,
        alignment: alignment,
        downloadable: downloadable,
        previewable: previewable
    };

    const requestData = {
        type: 'document',
        content: content,
        order: getNextOrder()
    };

    fetch(`/admin/lessons/${lessonId}/content-blocks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('contentBlocksContainer');
            if (container) {
                const emptyState = document.getElementById('emptyState');
                if (emptyState) emptyState.classList.add('d-none');
                container.insertAdjacentHTML('beforeend', data.blockHtml);
                initializeSortable();
            }
            if (typeof showNotification === 'function') {
                showNotification('Document block added successfully', 'success');
            }
            const modalEl = document.getElementById('documentBlockModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            resetDocumentForm();
        } else {
            alert('Error: ' + (data.message || 'Unable to save document block'));
        }
    })
    .catch(err => {
        console.error('Save error:', err);
        alert('Error saving document block');
    });
}

function resetDocumentForm() {
    document.getElementById('documentBlockForm').reset();
    document.getElementById('documentFileInfo').classList.add('d-none');
    document.getElementById('documentUploadProgress').classList.add('d-none');
    document.getElementById('documentProgressBar').style.width = '0%';
    document.getElementById('documentProgressText').textContent = '0%';
    selectedDocumentFile = null;
}
</script>

