@php
    use App\Enums\Category;
    use App\Enums\TaskStatus;
    use App\Enums\TaskPriorities;
@endphp

<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <!-- Task Input -->
    <div class="mb-3">
        <label for="title" class="form-label">Task <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="title" placeholder="Enter Task" required>
    </div>

    <!-- Assigned to External -->
    <div class="mb-3">
        <label class="form-label">Assigned to</label>
        <input type="text" class="form-control" name="assigned_to_external"
               placeholder="Enter person name">
    </div>

    <!-- Start Date & Due Date -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Start Date</label>
            <input type="datetime-local" class="form-control" name="date_start">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Due Date <span class="text-danger">*</span></label>
            <input type="datetime-local" class="form-control" name="date_due" required>
        </div>
    </div>

    <!-- Tags & Category -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Tag</label>
            <input type="text" class="form-control" name="tag" placeholder="Type tag">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select class="form-select" name="category" required>
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
            <select class="form-select" name="priority">
                <!-- Populate with task statuses dynamically -->
                @foreach(TaskPriorities::labels() as $priority => $label)
                    <option value="{{ $priority }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Task Status</label>
            <select class="form-select" name="status">
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
            <input type="range" class="form-range" name="progress" value="0" min="0" max="100">
        </div>
        {{--        <div class="col-md-6 mb-3">--}}
        {{--            <label class="form-label">Remind me on:</label>--}}
        {{--            <input type="date" class="form-control" name="date_remind">--}}
        {{--        </div>--}}
    </div>

    <!-- Description -->
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="5"
                  placeholder="Enter task description"></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end">
        {{--        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>--}}
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
