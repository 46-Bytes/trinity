<form method="POST" action="{{ route('settings.store') }}">
    @csrf
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" class="form-control" id="title" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" id="description" required></textarea>
    </div>
    <div class="mb-3">
        <label for="setting_name" class="form-label">Setting Name</label>
        <input type="text" name="setting_name" class="form-control" id="setting_name" required>
    </div>

    <div class="mb-3">
        <label for="setting_value" class="form-label">Setting Value</label>
        <textarea name="setting_value" class="form-control" id="setting_value" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Create</button>
</form>
