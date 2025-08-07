<form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="file" class="form-label">Select File</label>
            <input type="file" class="form-control" name="file" id="file" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Upload File</button>
</form>
