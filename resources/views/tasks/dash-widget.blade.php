@php use App\Enums\Category; @endphp
<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Tasks</span>
            <span class="fs-7 fw-semibold text-muted">Showing {{ $tasks->count() }} tasks</span>
        </h3>
        <div class="card-toolbar">
            <x-metronic-modal buttonName='<i class="fa fa-solid fa-list-check"></i> New Task'
                              modalHeaderText="New Task"
                              modalView="tasks.modal-dash-create" dataBsTarget="kt_modal_create_task"/>
        </div>
    </div>
    <div class="card-body pt-9 pb-0 mb-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable" id="tasksTable">
            <thead>
            <tr class="text-start fw-bold text-gray-600 fs-7 text-uppercase gs-0">
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($tasks as $task)
                @php
                    $taskCategory=Category::from($task->category);
                @endphp
                <tr>
                    <!-- Task Title (Clickable) -->
                    {{--                    <td><a href="#" class="editTaskButton" data-id="{{ $task->id }}">{{ $task->title }}</a></td>--}}
                    <!-- Task Title (Clickable) -->
                    <td>
                        <a href="#" class="editTaskButton"
                           data-id="{{ $task->id }}"
                           data-title="{{ $task->title }}"
                           data-category="{{ $task->category }}"
                           data-date_due="{{ $task->date_due }}"
                           data-priority="{{ $task->priority }}"
                           data-status="{{ $task->status }}"
                           data-progress="{{ $task->progress }}"
                           data-description="{{ $task->description }}"
                           data-bs-toggle="modal"
                           data-bs-target="#kt_modal_edit_task">
                            {{ $task->title }}
                        </a>
                    </td>

                    <!-- Category Colored Text -->
                    <td style="color: {{ $taskCategory->color() ?? '#000000' }}">{{ $taskCategory->label() }}</td>

                    <!-- Status Badge -->
                    <td>
                        @php
                            $statusClasses = [
                                'needs-action' => 'badge-danger',
                                'in-progress' => 'badge-warning',
                                'completed' => 'badge-success',
                                'cancelled' => 'badge-info'
                            ];
                            $statusClass = $statusClasses[$task->status] ?? 'badge-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($task->status) }}</span>
                    </td>

                    <!-- Progress -->
                    <td>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $task->progress }}%;" aria-valuenow="{{ $task->progress }}"
                                 aria-valuemin="0" aria-valuemax="100">{{ $task->progress }}%
                            </div>
                        </div>
                    </td>

                    <!-- Task Priority -->
                    <td>
                        @php
                            $priorityClasses = [
                                'low' => 'badge-success',
                                'medium' => 'badge-primary',
                                'high' => 'badge-danger',
                                'critical' => 'badge-info'
                            ];
                            $priorityClass = $priorityClasses[$task->priority] ?? 'badge-secondary';
                        @endphp
                        <span class="badge {{ $priorityClass }}">{{ ucfirst($task->priority) }}</span>
                    </td>

                    <!-- Due Date with Conditional Color Coding -->
                    <td>
                        @php
                            $dueDateClass = 'text-success'; // Default: more than a week away
                            if ($task->date_due) {
                                $daysDifference = now()->diffInDays($task->date_due, false);
                                if ($daysDifference <= 0) {
                                    $dueDateClass = 'text-danger'; // Today or overdue
                                } elseif ($daysDifference <= 3) {
                                    $dueDateClass = 'text-warning'; // Less than 3 days
                                }
                            }
                        @endphp
                        <span class="{{ $dueDateClass }}">
                            {{ $task->date_due ? $task->date_due->format('Y-m-d') : 'No Due Date' }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td>
                        <div class="d-flex">
                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" class="me-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Mark Complete">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <a href="#" class="me-2 btn btn-sm btn-primary editTaskButton"
                               data-id="{{ $task->id }}"
                               data-title="{{ $task->title }}"
                               data-category="{{ $task->category }}"
                               data-date_due="{{ $task->date_due }}"
                               data-priority="{{ $task->priority }}"
                               data-status="{{ $task->status }}"
                               data-progress="{{ $task->progress }}"
                               data-description="{{ $task->description }}"
                               data-bs-toggle="modal"
                               data-bs-target="#kt_modal_edit_task">
                                <i class="fas fa-tools"></i>
                            </a>
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('tasks.modal-dash-edit')

<!-- Your Custom Script -->
<script>
    $(document).ready(function () {
        $('#tasksTable').DataTable({
            "paging": false,
            "searching": true,
            language: {
                info: "", // Removes the text
            },
            "ordering": true,
            "order": [[2, 'asc']],
            "columnDefs": [
                {"orderable": true}
            ],
            "stateSave": true // This saves the state of the table (page, ordering, etc.)
        });


        $('.editTaskButton').on('click', function () {
            var taskId = $(this).data('id');
            // Other task data assignments...
            var taskTitle = $(this).data('title');
            var taskCategory = $(this).data('category');
            var taskDateDue = $(this).data('date_due');
            var taskPriority = $(this).data('priority');
            var taskStatus = $(this).data('status');
            var taskProgress = $(this).data('progress');
            var taskDescription = $(this).data('description');

            // Set the form values
            $('#edit-title').val(taskTitle);
            $('#edit-category').val(taskCategory);
            $('#edit-date_due').val(taskDateDue);
            $('#edit-priority').val(taskPriority);
            $('#edit-status').val(taskStatus);
            $('#edit-description').val(taskDescription);
            $('#edit-progress').val(taskProgress);

            // Update the form action dynamically with the correct task ID
            $('#editTaskForm').attr('action', '/tasks/' + taskId);
        });
    });
</script>
