<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Notes</span>
        </h3>
        <div class="card-toolbar">
            <x-metronic-modal buttonName='<i class="fa fa-solid fa-sticky-note"></i> New Note'
                              modalHeaderText="New Note"
                              modalView="notes.modal-create" dataBsTarget="kt_modal_create_note"/>
        </div>
    </div>
    <div class="card-body pt-9 pb-0 mb-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="tableNoteList">
            <thead>
            <tr class="text-start fw-bold text-gray-600 fs-7 text-uppercase gs-0">
                <th>Title</th>
                <th>Color</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($notes as $note)
                <tr>
                    <td>{{ $note->title }}</td>
                    <td>{{ $note->color }}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
