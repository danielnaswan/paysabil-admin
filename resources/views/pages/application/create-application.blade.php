@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Register New Application</h6>
                            <a href="{{ route('application.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                        <p class="text-sm mb-0 mt-2">Submit a new student application for Sabil Al-Hikmah program</p>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data"
                            role="form text-left" id="applicationForm">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <span class="alert-text">
                                            <strong>Validation Error:</strong> {{ $errors->first() }}
                                        </span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                        <i class="fa fa-close" aria-hidden="true"></i>
                                    </button>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" id="alert-success"
                                    role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <span class="alert-text">{{ session('success') }}</span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                        <i class="fa fa-close" aria-hidden="true"></i>
                                    </button>
                                </div>
                            @endif

                            <!-- Student Selection Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-primary">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-user me-2"></i>Student Selection
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="student_id" class="form-control-label">
                                            Select Student <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control @error('student_id') is-invalid @enderror"
                                            id="student_id" name="student_id" required>
                                            <option value="">Choose a student...</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ old('student_id') == $student->id ? 'selected' : '' }}
                                                    data-email="{{ $student->user->email ?? 'N/A' }}"
                                                    data-phone="{{ $student->user->phone_number ?? 'N/A' }}"
                                                    data-completion="{{ $student->profile_completion_percentage ?? 0 }}">
                                                    {{ $student->full_name }} ({{ $student->matrix_no }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('student_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- Student Info Display -->
                                        <div id="studentInfo" class="mt-3" style="display: none;">
                                            <div class="alert alert-info text-white">
                                                <h6 class="mb-2 text-white">Selected Student Information:</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Email:</strong> <span
                                                                id="studentEmail">-</span></p>
                                                        <p class="mb-0"><strong>Phone:</strong> <span
                                                                id="studentPhone">-</span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Profile Completion:</strong> <span
                                                                id="studentCompletion">-</span>%</p>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-gradient-primary" id="completionBar"
                                                                style="width: 0%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Application Details Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-info">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Application Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="title" class="form-control-label">
                                            Application Title <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control @error('title') is-invalid @enderror" type="text"
                                            id="title" name="title" value="{{ old('title') }}"
                                            placeholder="Enter a descriptive title for this application..." maxlength="255"
                                            required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Minimum 5 characters required</small>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="description" class="form-control-label">
                                            Description <span class="text-muted">(Optional)</span>
                                        </label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="4" maxlength="1000" placeholder="Provide additional details about this application...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Maximum 1000 characters</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Upload Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-warning">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-upload me-2"></i>Supporting Document
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="document" class="form-control-label">
                                            Upload PDF Document <span class="text-danger">*</span>
                                        </label>
                                        <div class="upload-area border-dashed border-2 p-4 text-center rounded @error('document') border-danger @enderror"
                                            id="uploadArea">
                                            <div class="upload-content">
                                                <i class="fas fa-cloud-upload-alt text-muted display-4 mb-3"></i>
                                                <h6 class="text-muted mb-2">Drag and drop your PDF file here</h6>
                                                <p class="text-sm text-muted mb-3">or click to browse</p>
                                                <input class="form-control d-none @error('document') is-invalid @enderror"
                                                    type="file" id="document" name="document"
                                                    accept="application/pdf" required>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="document.getElementById('document').click()">
                                                    <i class="fas fa-file-pdf me-2"></i>Choose PDF File
                                                </button>
                                            </div>
                                            <div class="file-preview" id="filePreview" style="display: none;">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-pdf text-danger fa-2x me-3"></i>
                                                        <div>
                                                            <h6 class="mb-0" id="fileName">filename.pdf</h6>
                                                            <p class="text-sm text-muted mb-0" id="fileSize">0 MB</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="removeFile()">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @error('document')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Only PDF files are allowed. Maximum file size: 10MB
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Important Notes Section -->
                            <div class="card border-light mb-4">
                                <div class="card-header bg-gradient-success">
                                    <h6 class="text-white mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Important Notes
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0 text-sm">
                                        <li>Make sure all information is accurate before submitting</li>
                                        <li>The supporting document should be relevant to the application</li>
                                        <li>Applications will be reviewed within 5 business days</li>
                                        <li>You will be notified of the decision via email</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="card border-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">
                                                <span class="text-danger">*</span> Required fields
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('application.index') }}" class="btn btn-light me-2">
                                                <i class="fas fa-times me-1"></i>Cancel
                                            </a>
                                            <button type="submit" class="btn bg-gradient-primary" id="submitBtn">
                                                <i class="fas fa-paper-plane me-1"></i>Submit Application
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .upload-area {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            background-color: #f8f9fa;
        }

        .upload-area.border-dashed {
            border-style: dashed !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentSelect = document.getElementById('student_id');
            const studentInfo = document.getElementById('studentInfo');
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('document');
            const submitBtn = document.getElementById('submitBtn');

            // Student selection handler - Fixed to properly handle data attributes
            studentSelect.addEventListener('change', function() {
                console.log('Student selection changed:', this.value); // Debug log

                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];

                    // Get data attributes with fallback values
                    const email = selectedOption.getAttribute('data-email') || 'N/A';
                    const phone = selectedOption.getAttribute('data-phone') || 'N/A';
                    const completion = selectedOption.getAttribute('data-completion') || '0';

                    console.log('Selected option data:', {
                        email,
                        phone,
                        completion
                    }); // Debug log

                    // Update the display elements
                    const emailElement = document.getElementById('studentEmail');
                    const phoneElement = document.getElementById('studentPhone');
                    const completionElement = document.getElementById('studentCompletion');
                    const completionBar = document.getElementById('completionBar');

                    if (emailElement) emailElement.textContent = email;
                    if (phoneElement) phoneElement.textContent = phone;
                    if (completionElement) completionElement.textContent = completion;
                    if (completionBar) completionBar.style.width = completion + '%';

                    // Show the student info div
                    studentInfo.style.display = 'block';
                } else {
                    // Hide the student info div when no student is selected
                    studentInfo.style.display = 'none';
                }
            });

            // File upload handlers
            uploadArea.addEventListener('click', function(e) {
                // Prevent triggering if clicking on the remove button
                if (!e.target.closest('.btn-outline-danger')) {
                    fileInput.click();
                }
            });

            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('bg-light');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('bg-light');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('bg-light');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0]);
                }
            });

            fileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });

            function handleFileSelect(file) {
                console.log('File selected:', file.name, file.type, file.size); // Debug log

                // Validate file type
                if (file.type !== 'application/pdf') {
                    alert('Please select a PDF file only.');
                    fileInput.value = '';
                    return;
                }

                // Validate file size (10MB = 10485760 bytes)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (file.size > maxSize) {
                    alert('File size must be less than 10MB. Selected file is ' + (file.size / (1024 * 1024))
                        .toFixed(2) + 'MB');
                    fileInput.value = '';
                    return;
                }

                // Update preview
                const fileNameElement = document.getElementById('fileName');
                const fileSizeElement = document.getElementById('fileSize');
                const uploadContent = document.querySelector('.upload-content');
                const filePreview = document.getElementById('filePreview');

                if (fileNameElement) fileNameElement.textContent = file.name;
                if (fileSizeElement) fileSizeElement.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                if (uploadContent) uploadContent.style.display = 'none';
                if (filePreview) filePreview.style.display = 'block';
            }

            // Form submission handler
            document.getElementById('applicationForm').addEventListener('submit', function(e) {
                // Basic client-side validation
                const studentId = document.getElementById('student_id').value;
                const title = document.getElementById('title').value.trim();
                const document = document.getElementById('document').files[0];

                if (!studentId) {
                    e.preventDefault();
                    alert('Please select a student.');
                    return;
                }

                if (!title || title.length < 5) {
                    e.preventDefault();
                    alert('Please enter a title with at least 5 characters.');
                    document.getElementById('title').focus();
                    return;
                }

                if (!document) {
                    e.preventDefault();
                    alert('Please upload a PDF document.');
                    return;
                }

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';
                submitBtn.disabled = true;
            });

            // Initialize student info display if there's an old value
            const oldStudentId = '{{ old('student_id') }}';
            if (oldStudentId) {
                // Trigger change event to show student info for old selection
                studentSelect.dispatchEvent(new Event('change'));
            }
        });

        function removeFile() {
            const fileInput = document.getElementById('document');
            const uploadContent = document.querySelector('.upload-content');
            const filePreview = document.getElementById('filePreview');

            if (fileInput) fileInput.value = '';
            if (uploadContent) uploadContent.style.display = 'block';
            if (filePreview) filePreview.style.display = 'none';
        }
    </script>
@endsection
