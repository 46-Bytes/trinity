<x-app-layout>
    @php
        if(!$formEntry){
            $formEntry = $diagnostic->form_entry;
        }
    @endphp
    <style>
        .spinner {
            margin: 20px auto;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .uploading-file {
            color: orange;
            font-style: italic;
        }

        .uploaded-file {
            color: green;
            font-weight: bold;
        }

        .uploaded-files-container {
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>

    <div class="d-flex flex-column flex-lg-row h-100">
        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10 d-flex flex-column h-100">
            <div class="card flex-grow-1 d-flex flex-column h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Diagnostic</span>
                        <span class="text-muted mt-1 fs-7">Answering these questions will help us understand your business better and provide relevant advice when needed.</span>
                    </h3>
                </div>

                <!-- SurveyJS Form Placeholder -->
                <div id="surveyjsDiagnostic" class="card-footer overflow-auto"></div>

                <!-- Manual Save Button -->
                <div class="card-footer border-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted fs-7">
                            <i class="fas fa-info-circle me-1"></i>
                            Your progress is automatically saved as you fill out the form
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="saveAndExitBtn" class="btn btn-light-primary">
                                <i class="fas fa-save me-1"></i>
                                Save & Exit to Dashboard
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manually Added Uploaded Files Container -->
                <div id="uploadedFilesList" class="uploaded-files-container"></div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log('DOM Content Loaded - Initializing SurveyJS');
            const surveyJSON = {!! $surveyJson !!};
            const savedResponses = {!! $formEntry->responses ?? '{}' !!};
            const surveyJSTheme = {!! $themeJson !!};
            const activePage = {!! json_encode($formEntry->active_page) !!};

            // Initialize SurveyJS
            const survey = new Survey.Model(surveyJSON);
            console.log('SurveyJS Model created:', survey);
            survey.applyTheme(surveyJSTheme);
            console.log('SurveyJS Theme applied');

            survey.completedHtml = `
            <div style="text-align: center;">
                <h3>Thank you for completing the diagnostic!</h3>
                <p>Your advice is being prepared. Please wait...</p>
                <p>This may take a few minutes. Please stay on this page.</p>
                <div class="spinner"></div>
            </div>`;

            survey.data = savedResponses;
            console.log('Survey data loaded:', savedResponses);

            // Test if file upload event is connected
            console.log('Checking if onUploadFiles event exists:', typeof survey.onUploadFiles);
            if (survey.onUploadFiles) {
                console.log('onUploadFiles event exists, adding handler');
            } else {
                console.error('onUploadFiles event does not exist on survey object!');
            }

            // Add debug for page navigation
            survey.onCurrentPageChanging.add(function (sender, options) {
                console.log('Page changing from', options.oldCurrentPage?.name, 'to', options.newCurrentPage?.name);
                console.log('Current page has file upload questions:',
                    options.oldCurrentPage?.questions.some(q => q.getType() === 'file'));

                // Check if there are any file questions with values on the current page
                const fileQuestions = options.oldCurrentPage?.questions.filter(q => q.getType() === 'file') || [];
                console.log('File questions on current page:', fileQuestions.length);

                fileQuestions.forEach(question => {
                    console.log('File question:', question.name, 'has value:', question.value);
                    if (question.value && question.value.length > 0) {
                        console.log('Files selected but not uploaded:', question.value);

                        // Force upload if files are selected but not uploaded
                        console.log('Manually triggering upload for question:', question.name);

                        // Get the actual File objects from the DOM
                        const fileInput = document.querySelector(`input[type="file"][data-name="${question.name}"]`);
                        if (fileInput && fileInput.files && fileInput.files.length > 0) {
                            console.log('Found file input with files:', fileInput.files.length);

                            // Create a FormData object and append files
                            const formData = new FormData();
                            for (let i = 0; i < fileInput.files.length; i++) {
                                formData.append('files[]', fileInput.files[i]);
                                console.log(`Manually adding file to FormData: ${fileInput.files[i].name}`);
                            }

                            // Manually upload the files
                            console.log('Manually sending upload request to server...');
                            fetch("{{ route('formFiles.upload') }}", {
                                method: "POST",
                                credentials: "same-origin",
                                headers: {
                                    "X-CSRF-Token": "{{ csrf_token() }}",
                                    "Accept": "application/json"
                                },
                                body: formData,
                            })
                                .then(response => {
                                    console.log('Manual upload response received:', response.status);
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Manual upload successful:', data);
                                    if (data.success) {
                                        // Update the question value with the uploaded file data
                                        const uploadedFiles = data.files.map(file => ({
                                            name: file.name,
                                            content: file.url,
                                            type: file.name.split('.').pop()
                                        }));
                                        question.value = uploadedFiles;
                                        console.log('Updated question value with uploaded files');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error during manual file upload:', error);
                                });
                        } else {
                            console.warn('Could not find file input element for question:', question.name);
                        }
                    }
                });
            });

            // Add debug for file question value changes
            survey.onValueChanged.add(function (sender, options) {
                console.log(`Value changed for: ${options.name}`, options.value);

                // Check if this is a file question
                const question = survey.getQuestionByName(options.name);
                if (question && question.getType() === 'file') {
                    console.log('File question value changed:', options.value);

                    // Try to trigger the file upload immediately when files are selected
                    setTimeout(() => {
                        console.log('Checking if files need to be uploaded for question:', question.name);
                        const fileInput = document.querySelector(`input[type="file"][data-name="${options.name}"]`);
                        if (fileInput && fileInput.files && fileInput.files.length > 0) {
                            console.log('Files detected in input, attempting to trigger upload');

                            // Try to trigger the native SurveyJS upload
                            if (typeof question.uploadFiles === 'function') {
                                console.log('Calling question.uploadFiles()');
                                question.uploadFiles();
                            } else {
                                console.warn('question.uploadFiles is not a function');
                            }
                        }
                    }, 500); // Small delay to ensure the files are properly attached
                }

                saveSurveyProgress();
            });

            survey.onUploadFiles.add(function (sender, options) {
                console.log('File upload initiated', options.files);
                // Add a more visible console message for testing
                console.warn('UPLOAD EVENT FIRED - THIS SHOULD BE VISIBLE IN CONSOLE');
                console.log('Upload sender:', sender);
                console.log('Upload options:', JSON.stringify(options));
                const uploadedFilesContainer = document.getElementById("uploadedFilesList");

                if (!uploadedFilesContainer) {
                    console.error("The container for uploaded files was not found.");
                    return;
                }

                // Create a map to track file status elements
                const fileStatusElements = new Map();

                // Show files being added to the upload queue
                options.files.forEach((file, index) => {
                    console.log(`Processing file ${index}:`, file.name, file.size);
                    if (!file || typeof file.name !== "string") {
                        console.error(`Invalid file at index ${index}:`, file);
                        // alert("An invalid file was detected. Please try again.");
                        return;
                    }

                    // Create a status element for each file
                    const fileItem = document.createElement("div");
                    fileItem.innerText = `Uploading: ${file.name}...`;
                    fileItem.classList.add("uploading-file");
                    uploadedFilesContainer.appendChild(fileItem);
                    console.log(`Added status element for file: ${file.name}`);

                    // Track the element for later status updates
                    fileStatusElements.set(file.name, fileItem);
                });

                const formData = new FormData();
                options.files.forEach((file) => {
                    if (file && typeof file.name === "string") {
                        formData.append("files[]", file);
                        console.log(`Added to FormData: ${file.name}`);
                    }
                });

                console.log('Sending upload request to server...');
                // Upload files
                fetch("{{ route('formFiles.upload') }}", {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "X-CSRF-Token": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData,
                })
                    .then((response) => {
                        console.log('Server response received:', response.status);
                        if (!response.ok) {
                            console.error('Response not OK:', response.status, response.statusText);
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then((data) => {
                        console.log('Upload response data:', data);
                        if (data.success) {
                            data.files.forEach((uploadedFile) => {
                                console.log('Successfully uploaded file:', uploadedFile);
                                // Update the status element to "Uploaded"
                                const fileItem = fileStatusElements.get(uploadedFile.name);
                                if (fileItem) {
                                    fileItem.innerText = `Uploaded: ${uploadedFile.name}`;
                                    fileItem.classList.remove("uploading-file");
                                    fileItem.classList.add("uploaded-file");
                                    console.log(`Updated status for file: ${uploadedFile.name}`);
                                } else {
                                    console.warn(`Status element not found for file: ${uploadedFile.name}`);
                                }
                            });
                            console.log('Calling success callback with files:', data.files);
                            options.callback(
                                "success",
                                data.files.map((file) => ({
                                    fileId: file.id,
                                    fileName: file.name,
                                    content: file.url,
                                }))
                            );
                        } else {
                            console.error("Backend error:", data.message || "Unknown error");
                            throw new Error(data.message || "Unknown error occurred.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error during file upload:", error);
                        // alert(`An error occurred while uploading files: ${error.message}`);
                        console.log('Calling error callback');
                        options.callback("error");
                    });
            });

            survey.onComplete.add(function () {
                saveSurveyProgress(true); // Final save
                document.getElementById("surveyjsDiagnostic").innerHTML = survey.completedHtml;
                checkDiagnosticStatus({{ $formEntry->id }}); // Check status
            });

            function saveSurveyProgress(forceComplete = false, showNotification = false) {
                console.log('Saving survey progress...');

                // Create a copy of the survey data
                const surveyData = JSON.parse(JSON.stringify(survey.data));

                // Get all file-type questions from the survey
                const fileQuestions = [];
                survey.getAllQuestions().forEach(question => {
                    if (question.getType() === 'file') {
                        fileQuestions.push(question.name);
                    }
                });

                // Remove file-type question data to avoid storing large base64 content in DB
                fileQuestions.forEach(questionName => {
                    if (surveyData[questionName]) {
                        // Replace with just a reference that files were uploaded
                        surveyData[questionName] = '[FILE REFERENCES STORED SEPARATELY]';
                    }
                });

                console.log('Filtered out file data from survey data');

                return fetch("{{ route('diagnostic.saveFormEntry') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-Token": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        form_entry_id: "{{ $formEntry->id }}",
                        responses: JSON.stringify(surveyData),
                        active_page: survey.currentPage ? survey.currentPage.name : null,
                        percentage_complete: forceComplete ? 100 : survey.getProgress(),
                        is_completed: forceComplete
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Progress saved:", data);
                        if (showNotification) {
                            // Show a subtle notification that progress was saved
                            const notification = document.createElement('div');
                            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
                            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                            notification.innerHTML = `
                                <i class="fas fa-check-circle me-2"></i>
                                Progress saved successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(notification);
                            
                            // Auto-remove after 3 seconds
                            setTimeout(() => {
                                if (notification.parentNode) {
                                    notification.parentNode.removeChild(notification);
                                }
                            }, 3000);
                        }
                        return data;
                    })
                    .catch(error => {
                        console.error("Error saving progress:", error);
                        if (showNotification) {
                            // Show error notification
                            const notification = document.createElement('div');
                            notification.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                            notification.innerHTML = `
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Error saving progress. Please try again.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(notification);
                            
                            // Auto-remove after 5 seconds
                            setTimeout(() => {
                                if (notification.parentNode) {
                                    notification.parentNode.removeChild(notification);
                                }
                            }, 5000);
                        }
                        throw error;
                    });
            }

            function checkDiagnosticStatus(diagnosticId) {
                fetch(`/api/diagnostic-status/${diagnosticId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.adviceReady) {
                            window.location.href = "{{ route('dashboard') }}";
                        } else {
                            setTimeout(() => checkDiagnosticStatus(diagnosticId), 5000); // Retry
                        }
                    })
                    .catch(error => console.error("Error checking diagnostic status:", error));
            }

            survey.render("surveyjsDiagnostic");
            console.log('Survey rendered to DOM');

            // Add event listener for the manual save button
            document.getElementById('saveAndExitBtn').addEventListener('click', function() {
                console.log('Manual save button clicked');
                
                // Show loading state
                const saveBtn = this;
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
                saveBtn.disabled = true;
                
                // Save current progress with notification
                saveSurveyProgress(false, true)
                    .then(() => {
                        // Show success message and redirect
                        saveBtn.innerHTML = '<i class="fas fa-check me-1"></i>Saved! Redirecting...';
                        saveBtn.classList.remove('btn-light-primary');
                        saveBtn.classList.add('btn-success');
                        
                        // Redirect to dashboard after showing success
                        setTimeout(() => {
                            window.location.href = "{{ route('dashboard') }}";
                        }, 1000);
                    })
                    .catch(() => {
                        // Reset button on error
                        saveBtn.innerHTML = originalText;
                        saveBtn.disabled = false;
                        saveBtn.classList.remove('btn-success');
                        saveBtn.classList.add('btn-light-primary');
                    });
            });

            // Jump to the last active page
            if (activePage) {
                const pageIndex = survey.pages.findIndex(page => page.name === activePage);
                if (pageIndex !== -1) {
                    survey.currentPageNo = pageIndex; // Jump to the saved page index
                    console.log('Jumped to saved page:', activePage, 'index:', pageIndex);
                }
            }

            // Add direct event listeners to file inputs after rendering
            setTimeout(() => {
                console.log('Setting up direct file input event listeners');

                // Function to check for new file inputs and attach listeners
                function attachFileInputListeners() {
                    const fileInputs = document.querySelectorAll('input[type="file"]');
                    console.log('Found file inputs:', fileInputs.length);

                    fileInputs.forEach(input => {
                        // Skip if we've already attached a listener to this input
                        if (input.getAttribute('data-has-upload-listener') === 'true') {
                            return;
                        }

                        console.log('Adding change listener to file input:', input);
                        input.setAttribute('data-has-upload-listener', 'true');

                        input.addEventListener('change', function (event) {
                            console.log('File input change event fired', this.files);
                            if (this.files && this.files.length > 0) {
                                // Get the question name from data attribute or parent elements
                                const questionName = this.getAttribute('data-name') ||
                                    this.closest('[data-name]')?.getAttribute('data-name') ||
                                    this.closest('.sv_q')?.getAttribute('data-name');

                                console.log('File input belongs to question:', questionName);

                                // Validate file types and sizes before upload
                                let validFiles = true;
                                let errorMessage = '';

                                for (let i = 0; i < this.files.length; i++) {
                                    const file = this.files[i];
                                    console.log(`Validating file: ${file.name}, size: ${file.size}, type: ${file.type}`);

                                    // Check file size (100MB limit)
                                    if (file.size > 100 * 1024 * 1024) {
                                        validFiles = false;
                                        errorMessage = `File ${file.name} exceeds the 100MB size limit`;
                                        console.error(errorMessage);
                                        break;
                                    }

                                    // Check file type (adjust as needed)
                                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'text/csv', 'text/plain', 'image/jpeg', 'image/png'];
                                    if (!allowedTypes.includes(file.type)) {
                                        validFiles = false;
                                        errorMessage = `File ${file.name} has an unsupported file type`;
                                        console.error(errorMessage);
                                        break;
                                    }
                                }

                                if (!validFiles) {
                                    // Show error message
                                    const uploadedFilesContainer = document.getElementById("uploadedFilesList");
                                    if (uploadedFilesContainer) {
                                        const errorDiv = document.createElement('div');
                                        errorDiv.innerText = errorMessage;
                                        errorDiv.style.color = 'red';
                                        uploadedFilesContainer.appendChild(errorDiv);
                                    }
                                    // Reset the file input
                                    this.value = '';
                                    return;
                                }

                                // Create FormData and upload files
                                const formData = new FormData();
                                for (let i = 0; i < this.files.length; i++) {
                                    formData.append('files[]', this.files[i]);
                                    console.log(`Direct upload - adding file: ${this.files[i].name}`);
                                }

                                // Show visual feedback
                                const fileStatusDiv = document.createElement('div');
                                fileStatusDiv.innerText = `Uploading ${this.files.length} file(s)...`;
                                fileStatusDiv.classList.add('uploading-file');
                                const uploadedFilesContainer = document.getElementById("uploadedFilesList");
                                if (uploadedFilesContainer) {
                                    uploadedFilesContainer.appendChild(fileStatusDiv);
                                }

                                // Store reference to the file input
                                const fileInput = this;

                                // Upload the files directly
                                console.log('Direct upload - sending to server');
                                fetch("{{ route('formFiles.upload') }}", {
                                    method: "POST",
                                    credentials: "same-origin",
                                    headers: {
                                        "X-CSRF-Token": "{{ csrf_token() }}",
                                        "Accept": "application/json"
                                    },
                                    body: formData,
                                })
                                    .then(response => {
                                        console.log('Direct upload - response status:', response.status);
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Direct upload - success:', data);
                                        if (data.success) {
                                            // Update status
                                            fileStatusDiv.innerText = `Uploaded ${data.files.length} file(s) successfully!`;
                                            fileStatusDiv.classList.remove('uploading-file');
                                            fileStatusDiv.classList.add('uploaded-file');

                                            // If we have the question name, update the survey value
                                            if (questionName) {
                                                const question = survey.getQuestionByName(questionName);
                                                if (question) {
                                                    const uploadedFiles = data.files.map(file => ({
                                                        name: file.name,
                                                        content: file.url,
                                                        type: file.name.split('.').pop()
                                                    }));
                                                    question.value = uploadedFiles;
                                                    console.log('Updated question value with uploaded files');
                                                }
                                            }
                                        } else {
                                            fileStatusDiv.innerText = `Upload failed: ${data.message || 'Unknown error'}`;
                                            fileStatusDiv.classList.remove('uploading-file');
                                            fileStatusDiv.style.color = 'red';
                                            // Reset file input to allow retry
                                            fileInput.value = '';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error during direct file upload:', error);
                                        fileStatusDiv.innerText = `Upload error: ${error.message}`;
                                        fileStatusDiv.classList.remove('uploading-file');
                                        fileStatusDiv.style.color = 'red';
                                        // Reset file input to allow retry
                                        fileInput.value = '';
                                    });
                            }
                        });
                    });
                }

                // Initial attachment of listeners
                attachFileInputListeners();

                // Set up a mutation observer to watch for dynamically added file inputs
                const observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                            // Check if any new file inputs were added
                            let hasNewFileInputs = false;
                            mutation.addedNodes.forEach(node => {
                                if (node.querySelectorAll) {
                                    const fileInputs = node.querySelectorAll('input[type="file"]');
                                    if (fileInputs.length > 0) {
                                        hasNewFileInputs = true;
                                    }
                                }
                            });

                            if (hasNewFileInputs) {
                                console.log('New file inputs detected, attaching listeners');
                                attachFileInputListeners();
                            }
                        }
                    });
                });

                // Start observing the document with the configured parameters
                observer.observe(document.body, {childList: true, subtree: true});

                // Also attach listeners when navigating between survey pages
                survey.onCurrentPageChanged.add(function () {
                    console.log('Page changed, checking for new file inputs');
                    setTimeout(attachFileInputListeners, 500);
                });

            }, 1000); // Delay to ensure survey is fully rendered
        });
    </script>
</x-app-layout>
