@php
    use App\Enums\Category;
    use App\Enums\TaskStatus;
    use App\Enums\TaskPriorities;
@endphp
<div class="modal fade" id="kt_modal_edit_task" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Modify Task</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="modal-body scroll-y">
                <form id="editTaskForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Task <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>

                    <!-- Assigned to External -->
                    <div class="mb-3">
                        <label class="form-label">Assigned to</label>
                        <input type="text" class="form-control" id="edit-assigned_to_external"
                               name="assigned_to_external"
                               placeholder="Enter person name">
                    </div>

                    <!-- Start Date & Due Date -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" id="edit-date_start" class="form-control" name="date_start">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="edit-date_due" class="form-control" name="date_due"
                                   required>
                        </div>
                    </div>

                    <!-- Tags & Category -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tag</label>
                            <input type="text" class="form-control" id="edit-tags" name="tag" placeholder="Type tag">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-category" name="category" required>
                                <option selected disabled>Select category</option>
                                <!-- Populate with categories dynamically -->
                                @foreach(Category::labels() as $category => $label)
                                    <option value="{{ $category }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Priority & Task Status -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <select class="form-select" id="edit-priority" name="priority">
                                <!-- Populate with task statuses dynamically -->
                                @foreach(TaskPriorities::labels() as $priority => $label)
                                    <option value="{{ $priority }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Task Status</label>
                            <select class="form-select" id="edit-status" name="status">
                                <!-- Populate with task statuses dynamically -->
                                @foreach(TaskStatus::labels() as $status => $label)
                                    <option value="{{ $status }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Progress & Remind Me On -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Progress</label>
                            <input type="range" id="edit-progress" class="form-range" name="progress" value="0" min="0"
                                   max="100">
                        </div>
                        {{--                        <div class="col-md-6 mb-3">--}}
                        {{--                            <label class="form-label">Remind me on:</label>--}}
                        {{--                            <input type="date" id="edit-date_remind" class="form-control" name="date_remind">--}}
                        {{--                        </div>--}}
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit-description" name="description" rows="5"
                                  placeholder="Enter task description"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
