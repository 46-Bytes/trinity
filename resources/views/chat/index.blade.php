@php use App\Enums\Category; @endphp
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Chat"/>
    </x-slot>

    <style>
        /* Chat container styles */
        /* Style the chat message container */

        .card-body {
            height: 60vh; /* Set to your desired height */
            overflow-y: auto; /* Allow vertical scrolling */
        }

        #message-container {
            max-height: 60vh; /* Your desired height */
            overflow-y: auto; /* Enable vertical scrolling */
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        /* Customize user messages */
        .user-message {
            align-self: flex-end;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 20px 20px 0 20px;
            max-width: 75%;
            margin-bottom: 10px;
        }

        /* Customize assistant messages */
        .assistant-message {
            align-self: flex-start;
            background-color: #e4e6eb;
            color: #333;
            padding: 10px 15px;
            border-radius: 20px 20px 20px 0;
            max-width: 75%;
            margin-bottom: 10px;
        }

    </style>

    <div class="d-flex flex-column flex-lg-row h-100">
        <!-- Left Panel (Conversations List) -->
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
                    <x-metronic-modal buttonName="Create Conversation" modalHeaderText="New Conversation"
                                      modalView="chat.modal-create" dataBsTarget="kt_modal_create_conversation"
                                      :viewVars="['unusedCategories' => $unusedCategories]"/>

                    @foreach ($conversations as $conversation)
                        @php
                            $category = Category::from($conversation->category);
                            $isActive = $activeConversation && $activeConversation->id === $conversation->id;
                        @endphp
                        <div class="d-flex flex-stack py-4">
                            <div class="d-flex align-items-center">
                                <i class="fa-regular fa-circle {{ $isActive ? 'fa-beat-fade' : '' }}"
                                   style="color:  {{ $category->color() }};"></i>
                                <div class="ms-3">
                                    <a href="{{ route('chat.showConversation', $conversation->id) }}"
                                       class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">{{ $category->label() }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Panel (Active Chat) -->
        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10 d-flex flex-column h-100">
            <div class="card flex-grow-1 d-flex flex-column h-100" id="kt_chat_messenger">
                <!-- This select will only be visible on small devices (<= 768px) -->
                <div class="d-block d-lg-none pt-3 pb-2">


                    <x-metronic-modal buttonName="Create Conversation" modalHeaderText="New Conversation"
                                      modalView="chat.modal-create" dataBsTarget="kt_modal_create_conversation_mobile"
                                      :viewVars="['unusedCategories' => $unusedCategories]"/>


                    <select class="form-select" id="chatSelect">
                        <option selected disabled>Select Chat</option>
                        @foreach ($conversations as $conversation)
                            @php
                                $category = Category::from($conversation->category);
                            @endphp
                            <option value="{{ route('chat.showConversation', $conversation->id) }}">
                                {{ $category->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Inline script for redirection -->
                <script>
                    document.getElementById('chatSelect').addEventListener('change', function () {
                        const selectedRoute = this.value;
                        if (selectedRoute) {
                            window.location.href = selectedRoute;
                        }
                    });
                </script>

                @if($activeConversation)
                    <div class="card-header">
                        <div class="card-title">
                            <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary">{{ ucwords($activeConversation->category) }}</a>
                        </div>
                    </div>

                    <!-- Livewire Chat Messages Component -->
                    @livewire('chat-messages', ['conversationId' => $activeConversation->id])

                @else
                    <div class="alert alert-info">
                        No active conversation found. Please select or start a new conversation.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
