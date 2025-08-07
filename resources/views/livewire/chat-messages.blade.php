<div>

    <!-- Message Container -->
    <div class="card-body overflow-auto flex-grow-1" id="message-container">
        <div class="scroll-y me-n5 pe-5" id="chat-messages">
            @foreach ($messages as $message)
                <div
                        class="d-flex {{ $message['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-10">
                    <div class="d-flex flex-column align-items-{{ $message['role'] === 'user' ? 'end' : 'start' }}">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3" style="font-weight: bold;">
                                {{ $message['role'] === 'user' ? 'You' : 'TrinityAi' }}
                            </div>
                        </div>
                        <div
                                class="p-5 rounded bg-light-{{ $message['role'] === 'user' ? 'primary' : 'info' }} text-gray-900">
                            {!! convertMarkdownToHtml($message['message']) !!}
                            @if ($message['role'] === 'assistant')
                                <!-- Task and Note Icons -->
                                <div class="mt-3 d-flex">
                                    <!-- Task Icon -->
                                    <form action="{{ route('chat.createTaskFromMessage', $message['id']) }}" method="POST" style="display:inline; margin-right:15px" 
                                    onsubmit="this.querySelector('button').disabled = true; this.querySelector('.loading-spinner').style.display = 'inline-block';">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-muted me-3" title="Click to create tasks from this response">
                                            <span class="button-text"> 
                                                <i class="fas fa-tasks fa-lg" style="color:limegreen;font-size:20px;"></i> Generate Task
                                            </span>
                                            <span class="loading-spinner" style="display: none;">
                                                <i class="fas fa-spinner fa-spin"></i> Generating...
                                            </span>
                                        </button>
                                    </form>

                                    <!-- Note Icon -->
                                    <form action="{{ route('chat.createNoteFromMessage', $message['id']) }}" method="POST" style="display:inline;" 
                                    onsubmit="this.querySelector('button').disabled = true; this.querySelector('.loading-spinner').style.display = 'inline-block';">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-muted me-3" title="Click to create note from this response">
                                            <span class="button-text"> 
                                                <i class="fas fa-sticky-note fa-lg" style="color:gold;font-size:20px;"></i> Generate Note
                                            </span>
                                            <span class="loading-spinner" style="display: none;">
                                                <i class="fas fa-spinner fa-spin"></i> Generating...
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Message Input -->
    <div class="card-footer">
        <form wire:submit.prevent="sendMessage" class="d-flex">
            @csrf
            <input type="text" wire:model="newMessage" class="form-control" placeholder="Type a message..." required>
            <button type="submit" class="btn btn-primary ms-2" wire:loading.attr="disabled">
                <span wire:loading.remove>Send</span>
                <span wire:loading>Sending...</span>
            </button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to scroll to the bottom
            function scrollToBottom() {
                const container = document.getElementById('message-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }

            // Scroll on page load
            scrollToBottom();

            // Scroll whenever Livewire updates the messages


            // Target element to observe
            const targetNode = document.getElementById('message-container');

            // Callback function to execute when mutations are observed
            function callback(mutationsList, observer) {
                for (let mutation of mutationsList) {
                    if (mutation.type === 'childList') {
                        // Call your custom function here
                        scrollToBottom();
                    }
                }
            }


            // Create a MutationObserver instance
            const observer = new MutationObserver(callback);

            // Configuration object
            const config = {
                childList: true, // Observe changes to child elements
                subtree: true // Observe changes to all descendants
            };

            // Start observing the target node
            observer.observe(targetNode, config);


        });
    </script>

</div>
