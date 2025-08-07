NOT IN USE
{{--@php--}}
{{--    use App\Enums\Category;--}}
{{--@endphp--}}
{{--<x-app-layout>--}}
{{--    <x-slot name="header">--}}
{{--        <x-page-header title="Diagnostic" buttonName="Reset Conversation" modalHeaderText="Reset Conversation" modalView="diagnostic.modal-reset"--}}
{{--                       dataBsTarget="kt_modal_reset_conversation" :viewVars="['conversation' => $conversation]"/>--}}
{{--    </x-slot>--}}
{{--    <div class="d-flex flex-column flex-lg-row h-100">--}}
{{--        <!-- (Active Chat) -->--}}
{{--        <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10 d-flex flex-column h-100">--}}

{{--            <div class="card flex-grow-1 d-flex flex-column h-100" id="kt_chat_messenger">--}}

{{--                @if($conversation)--}}
{{--                    <div class="card flex-grow-1 d-flex flex-column h-100" id="kt_chat_messenger">--}}
{{--                        --}}{{--                        <div class="card-header">--}}
{{--                        --}}{{--                            <div class="card-title">--}}
{{--                        --}}{{--                                <a href="#"--}}
{{--                        --}}{{--                                   class="fs-4 fw-bold text-gray-900 text-hover-primary">{{ ucwords($conversation->category) }}</a>--}}
{{--                        --}}{{--                            </div>--}}
{{--                        --}}{{--                        </div>--}}

{{--                        <div class="card-body overflow-auto flex-grow-1" id="message-container">--}}
{{--                            <div class="scroll-y me-n5 pe-5" id="chat-messages">--}}
{{--                                @foreach ($messages as $message)--}}
{{--                                    <div class="d-flex {{ $message->role == 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-10">--}}
{{--                                        <div class="d-flex flex-column align-items-{{ $message->role == 'user' ? 'end' : 'start' }}">--}}
{{--                                            <div class="d-flex align-items-center mb-2">--}}
{{--                                                <div class="me-3" style="font-weight: bold;">--}}
{{--                                                    {{ $message->role == 'user' ? 'You' : 'TrinityAi' }}--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="p-5 rounded bg-light-{{ $message->role == 'user' ? 'primary' : 'info' }} text-gray-900">--}}
{{--                                                {!! convertMarkdownToHtml($message->message) !!}--}}

{{--                                                @if ($message->role == 'assistant')--}}
{{--                                                    <!-- Task and Note Icons -->--}}
{{--                                                    <div class="mt-3 d-flex">--}}
{{--                                                        <!-- Task Icon -->--}}
{{--                                                        <form action="{{ route('diagnostic.createTaskFromMessage', $message->id) }}" method="POST" style="display:inline;">--}}
{{--                                                            @csrf--}}
{{--                                                            <button type="submit" class="btn btn-link text-muted me-3" title="Click to create tasks from this response">--}}
{{--                                                                <i class="fas fa-tasks fa-lg" style="color:limegreen;font-size:20px;"></i>--}}
{{--                                                            </button>--}}
{{--                                                        </form>--}}
{{--                                                        <!-- Note Icon -->--}}
{{--                                                        <form action="{{ route('diagnostic.createNoteFromMessage', $message->id) }}" method="POST" style="display:inline;">--}}
{{--                                                            @csrf--}}
{{--                                                            <button type="submit" class="btn btn-link text-muted me-3" title="Click to create note from this response">--}}
{{--                                                                <i class="fas fa-sticky-note fa-lg" style="color:gold;font-size:20px;"></i>--}}
{{--                                                            </button>--}}
{{--                                                        </form>--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                        @if(!session('isDiagnosticComplete'))--}}
{{--                            <div class="card-footer">--}}
{{--                                <form method="POST"--}}
{{--                                      action="{{ route('diagnostic.sendMessage', ['conversationId' => $conversation->id]) }}">--}}
{{--                                    @csrf--}}
{{--                                    <textarea class="form-control mb-3" name="message" id="messageInput"--}}
{{--                                              placeholder="Type a message..."></textarea>--}}
{{--                                    <button type="submit" class="btn btn-primary">Send</button>--}}
{{--                                </form>--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                @else--}}
{{--                    <div class="alert alert-info">--}}
{{--                        No active conversation found. Please select or start a new conversation.--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Scroll to bottom script -->--}}
{{--    <script>--}}
{{--        // Scroll to bottom function--}}
{{--        function scrollToBottom() {--}}
{{--            const chatMessagesContainer = document.getElementById('chat-messages');--}}
{{--            if (chatMessagesContainer) {--}}
{{--                chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;--}}
{{--            }--}}
{{--        }--}}

{{--        // Scroll to bottom on page load--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            scrollToBottom();--}}
{{--        });--}}

{{--        // Scroll to bottom after message is submitted--}}
{{--        document.getElementById('chatForm').addEventListener('submit', function () {--}}
{{--            setTimeout(() => {--}}
{{--                scrollToBottom();--}}
{{--            }, 100); // Timeout to allow message to render--}}
{{--        });--}}
{{--    </script>--}}

{{--    <style>--}}
{{--        #kt_chat_messenger {--}}
{{--            display: flex;--}}
{{--            flex-direction: column;--}}
{{--        }--}}

{{--        #message-container {--}}
{{--            flex-grow: 1;--}}
{{--            max-height: 60vh; /* Adjust based on your layout */--}}
{{--        }--}}

{{--        #chat-messages {--}}
{{--            flex-grow: 1;--}}
{{--        }--}}

{{--        .mt-3.d-flex a {--}}
{{--            color: #6c757d; /* Text-muted color */--}}
{{--            transition: color 0.2s ease-in-out;--}}
{{--        }--}}

{{--        .mt-3.d-flex a:hover {--}}
{{--            color: #0d6efd; /* Blue on hover */--}}
{{--        }--}}

{{--    </style>--}}
{{--</x-app-layout>--}}
