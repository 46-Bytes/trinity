@php use App\Enums\Category; @endphp
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Notes"/>
    </x-slot>

    <div class="content flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column flex-lg-row">
            <!-- Sidebar -->
            <div class="d-none d-lg-flex flex-column flex-lg-row-auto w-100 w-lg-275px" data-kt-drawer="true"
                 data-kt-drawer-name="inbox-aside" data-kt-drawer-activate="{default: true, lg: false}"
                 data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start"
                 data-kt-drawer-toggle="#kt_inbox_aside_toggle">

                <div class="card card-flush mb-0" data-kt-sticky="false" data-kt-sticky-name="inbox-aside-sticky"
                     data-kt-sticky-offset="{default: false, xl: '100px'}" data-kt-sticky-width="{lg: '275px'}"
                     data-kt-sticky-left="auto" data-kt-sticky-top="100px" data-kt-sticky-animation="false"
                     data-kt-sticky-zindex="95">

                    <!-- Sidebar Menu -->
                    <div class="card-body">
                        <x-metronic-button buttonText="Compose" link="{{ route('notes.index') }}"/>
                        <div
                                class="menu menu-column menu-rounded menu-state-bg menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary mb-10">
                            @foreach ($menuItems as $item)
                                <x-metronic-menu-item :title="$item['title']" :route="$item['route']"/>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Content-->
            <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                <!--begin::Card-->
                <div class="card">

                    <!-- This select will only be visible on small devices (<= 768px) -->
                    <div class="d-block d-lg-none pt-3 pb-2">
                        <x-metronic-button buttonText="Compose" link="{{ route('notes.index') }}"/>
                        <select class="form-select" id="noteSelect">
                            <option selected disabled>Select Note</option>
                            @foreach ($menuItems as $item)
                                <option value="{{ $item['route'] }}">
                                    {{ $item['title'] }}
                                </option>
                            @endforeach
                        </select>

                    </div>


                    <!-- Inline script for redirection -->
                    <script>
                        document.getElementById('noteSelect').addEventListener('change', function () {
                            const selectedRoute = this.value;
                            if (selectedRoute) {
                                window.location.href = selectedRoute;
                            }
                        });
                    </script>

                    <div class="card-header d-flex align-items-center justify-content-between py-3">
                        <h2 class="card-title m-0">{{ isset($selectedNote) ? 'Edit Note' : 'Create Note' }}</h2>
                    </div>
                    <div class="card-body p-0">
                        <!--begin::Form-->
                        <form method="POST"
                              action="{{ isset($selectedNote) ? route('notes.update', $selectedNote->id) : route('notes.store') }}">
                            @csrf
                            @if(isset($selectedNote))
                                @method('PUT')
                            @endif
                            <!--begin::Body-->
                            <div class="p-6">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ isset($selectedNote) ? $selectedNote->title : '' }}" required>
                            </div>

                            <div class="p-6">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    @foreach(Category::labels() as $category => $label)
                                        <option
                                                value="{{ $category }}" {{ isset($selectedNote) && $selectedNote->category == $category ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 p-6">
                                @php
                                    $content = isset($selectedNote) ? $selectedNote->content : '';
                                @endphp
                                <x-forms.tinymce-editor :content="$content"/>
                            </div>
                            <!--end::Body-->

                            <!--begin::Footer-->
                            <div class="d-flex justify-content-between flex-wrap gap-2 py-5 ps-8 pe-5 border-top">
                                <!--begin::Save Changes (Left Aligned)-->
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($selectedNote) ? 'Save Changes' : 'Create Note' }}
                                </button>
                                <!--end::Save Changes-->

                                @if(isset($selectedNote))
                                    <!--begin::Actions (Right Aligned)-->
                                    <div class="d-flex gap-3 align-items-center">
                                        <!--begin::Color Picker-->
                                        <select class="form-select form-select-sm" name="note_color"
                                                onchange="event.preventDefault(); document.getElementById('color-form').submit();">
                                            @foreach(config('settings.note_colors') as $colorName => $colorHex)
                                                <option
                                                        value="{{ $colorHex }}" {{ $selectedNote && $selectedNote->color == $colorHex ? 'selected' : '' }}>{{ ucwords($colorName) }}</option>
                                            @endforeach
                                        </select>
                                        <form id="color-form"
                                              action="{{ route('notes.updateColor', $selectedNote->id) }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                        <!--end::Color Picker-->

                                        <!--begin::Pin Icon-->
                                        <a href="#"
                                           onclick="event.preventDefault(); document.getElementById('toggle-pin-form').submit();">
                                            <i class="fas fa-thumbtack fa-lg {{ $selectedNote->is_pinned ? 'text-warning' : '' }}"></i>
                                        </a>
                                        <form id="toggle-pin-form"
                                              action="{{ route('notes.togglePin', $selectedNote->id) }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                        <!--end::Pin Icon-->

                                        <!--begin::Delete Icon-->
                                        <a href="#"
                                           onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this note?')) { document.getElementById('delete-note-form').submit(); }">
                                            <i class="fas fa-trash fa-lg text-danger"></i>
                                        </a>
                                        <form id="delete-note-form"
                                              action="{{ route('notes.destroy', $selectedNote->id) }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <!--end::Delete Icon-->
                                    </div>
                                    <!--end::Actions-->
                                @endif

                            </div>
                            <!--end::Footer-->

                        </form>
                        <!--end::Form-->
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Inbox App - Compose -->
    </div>

</x-app-layout>
