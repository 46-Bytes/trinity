@php use App\Enums\FileType; @endphp
<div class="card mb-5 mb-xxl-8">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Files</span>
        </h3>
        {{--        <div class="card-toolbar">--}}
        {{--            <x-metronic-modal buttonName='<i class="fa fa-solid fa-upload"></i> Upload File'--}}
        {{--                              modalHeaderText="Upload File"--}}
        {{--                              modalView="files.modal-upload" dataBsTarget="kt_modal_upload_file"/>--}}
        {{--        </div>--}}
    </div>
    <div class="card-body pt-2 pb-0 mb-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable" id="filesTable">
            {{--            <thead>--}}
            {{--            <tr class="text-start fw-bold text-gray-600 fs-7 text-uppercase gs-0">--}}
            {{--                <th>Name</th>--}}
            {{--                <th>Description</th>--}}
            {{--                --}}{{--                <th>Actions</th>--}}
            {{--            </tr>--}}
            {{--            </thead>--}}
            <tbody>
            @foreach ($files as $file)
                @php
                    $fileType=FileType::from($file->file_type);
                @endphp
                <tr>
                    <td><a href="{{ $file->getDownloadUrl() }}"><i class="{{$fileType->icon()}} me-2" style="color:#22b469;font-size:20px;"></i>{{ $file->file_name }}</a></td>
                    <td>{{ $file->description }}</td>
                    {{--                    <td>--}}
                    {{--                        <a href="{{ $file->file_path }}" class="btn btn-sm btn-primary">Download</a>--}}
                    {{--                        <a href="#" class="btn btn-sm btn-danger">Delete</a>--}}
                    {{--                    </td>--}}
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{--    <script>--}}
    {{--        $(document).ready(function () {--}}
    {{--            $('#filesTable').DataTable({--}}
    {{--                "paging": true,--}}
    {{--                "searching": true,--}}
    {{--                "ordering": true,--}}
    {{--                "order": [[0, 'asc']],--}}
    {{--                "columnDefs": [--}}
    {{--                    {"orderable": true}--}}
    {{--                ],--}}
    {{--                "stateSave": true // This saves the state of the table (page, ordering, etc.)--}}
    {{--            });--}}
    {{--        });--}}
    {{--    </script>--}}
</div>
