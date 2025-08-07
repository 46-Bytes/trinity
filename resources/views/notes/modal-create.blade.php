<form action="{{ route('notes.store') }}" method="POST">
    @csrf
    <!-- Title Input -->
    <div class="mb-3">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="title" placeholder="Enter Title" required>
    </div>

    <!-- Note Textarea -->
    <div class="mb-3">
        <label class="form-label">Notes <span class="text-danger">*</span></label>
        <textarea class="form-control" name="content" rows="5" placeholder="Enter text here"
                  required></textarea>
    </div>

    <div class="mb-4">
        <label for="category" class="form-label">Category</label>
        <select name="category" id="category" class="form-control">
            <option value="">-- Select Category --</option>
            @foreach(['personal', 'general', 'products-or-services', 'structure', 'financial', 'human-resources', 'operations', 'sales-marketing', 'customers', 'technology', 'future-proofing', 'legal-licensing'] as $category)
                <option value="{{ $category }}">{{ ucwords(str_replace('-', ' ', $category)) }}</option>
            @endforeach
        </select>
    </div>
    <!-- Tag Input -->
    <div class="mb-3">
        <label class="form-label">Tag</label>
        <input type="text" class="form-control" name="tag" placeholder="Type tag">
    </div>

    <!-- Colour Input -->
    <div class="mb-3">
        <label class="form-label">Colour</label>
        <select class="form-select" name="color">
            @foreach(array_keys(config('settings.note_colors')) as $color)
                <option value="{{ $color }}">{{ ucwords($color) }}</option>
            @endforeach
        </select>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end">
        {{--        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>--}}
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
