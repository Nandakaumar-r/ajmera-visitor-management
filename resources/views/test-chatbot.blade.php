@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-10rem)] pt-24"> <!-- Adjusted height to account for header and footer -->
    <div class="container mx-auto h-full px-4">
        <div class="max-w-12xl mx-auto h-full">
            <div class="flex flex-col h-full bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="bg-primary-600 px-6 py-4 rounded-t-lg">
                    <h5 class="text-lg  font-semibold">HR Assistant</h5>
                </div>

                <!-- Messages Container -->
                <div class="flex-1 overflow-y-auto bg-gray-50">
                    <div id="chat-messages" class="p-4 space-y-4">
                        <!-- Initial greeting message -->
                        <div class="flex items-start max-w-[80%]">
                            <div class="bg-white border border-gray-200 rounded-lg rounded-bl-none px-4 py-2 shadow-sm">
                                <p class="text-gray-800">Hello! I'm <b>NexoHR</b>, your HR Assistant. How can I help you today?</p>
                            </div>
                        </div>
                    </div>

                    <div id="typing-indicator" class="hidden px-4 py-2">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>

                <!-- Input Container -->
                <div class="border-t border-gray-200 bg-white p-4 rounded-b-lg">
                    <form id="chat-form" class="flex items-center space-x-2">
                        @csrf
                        <input 
                            type="text" 
                            id="message-input" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                            placeholder="Type your message..." 
                            required
                        >
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Custom styles for user messages */
.user-message {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1rem;
}

.user-message .message-content {
    background-color: rgb(37 99 235);
    color: white;
    border-radius: 0.5rem;
    border-bottom-right-radius: 0;
    padding: 0.75rem 1rem;
    max-width: 80%;
}

/* Animation keyframes for typing indicator */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-0.5rem);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>
@endpush

@push('scripts')
<script>
// Define submitFeedback globally
async function submitFeedback(messageId, rating) {
    try {
        const response = await fetch('/chatbot/feedback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: messageId,
                rating: rating
            })
        });

        if (response.ok) {
            // Disable the feedback buttons for this message
            const feedbackContainer = document.querySelector(`.feedback-icons[data-message-id="${messageId}"]`);
            if (feedbackContainer) {
                feedbackContainer.querySelectorAll('button').forEach(button => {
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                });
                
                // Show a thank you message
                const thankYou = document.createElement('span');
                thankYou.textContent = 'Thank you for your feedback!';
                thankYou.classList.add('text-sm', 'text-gray-500', 'ml-2');
                feedbackContainer.appendChild(thankYou);
            }
        }
    } catch (error) {
        console.error('Error submitting feedback:', error);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const typingIndicator = document.getElementById('typing-indicator');
    
    let currentMessageId = null;

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        // Add user message to chat
        const userMessageHtml = `
            <div class="flex items-start justify-end mb-4">
                <div class="bg-blue-500 text-white rounded-lg p-3 max-w-[80%]">
                    <div class="whitespace-pre-wrap">${message}</div>
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', userMessageHtml);
        
        const currentMessage = message;
        messageInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;
        chatMessages.lastElementChild.scrollIntoView({ behavior: 'smooth' });

        // Show typing indicator
        typingIndicator.classList.remove('hidden');
        
        try {
            const response = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    message: currentMessage,
                    platform: 'web'
                })
            });

            const data = await response.json();
            console.log('Received data:', data);
            
            typingIndicator.classList.add('hidden');
            
            // Extract the response text from the data
            const botResponse = data.data.response;
            currentMessageId = data.data.id;
            
            const botMessageHtml = `
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white text-sm">HR</span>
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-[80%]">
                        <div class="text-gray-800 whitespace-pre-wrap">${botResponse}</div>
                        <div class="flex items-center justify-end mt-2 space-x-2 feedback-icons" data-message-id="${currentMessageId}">
                            <button onclick="submitFeedback(${currentMessageId}, 5)" class="text-gray-400 hover:text-green-500 transition-colors duration-200" title="Helpful">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                                </svg>
                            </button>
                            <button onclick="submitFeedback(${currentMessageId}, 1)" class="text-gray-400 hover:text-red-500 transition-colors duration-200" title="Not Helpful">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', botMessageHtml);
            // scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatMessages.lastElementChild.scrollIntoView({ behavior: 'smooth' });
        } catch (error) {
            console.error('Error:', error);
            typingIndicator.classList.add('hidden');
            
            const errorMessageHtml = `
                <div class="flex items-start mb-4">
                    <div class="bg-red-100 text-red-700 rounded-lg p-3 max-w-[80%]">
                        <div class="whitespace-pre-wrap">Sorry, I encountered an error. Please try again later.</div>
                    </div>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', errorMessageHtml);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatMessages.lastElementChild.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>
@endpush
