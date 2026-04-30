<template>
  <div class="fixed bottom-4 right-4 z-50">
    <!-- Chat Button -->
    <button
      v-if="!isOpen"
      @click="toggleChat"
      class="bg-primary-600 hover:bg-primary-700 text-white rounded-full p-4 shadow-lg flex items-center"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z" />
      </svg>
      <span class="ml-2">HR Assistant</span>
    </button>

    <!-- Chat Window -->
    <div
      v-if="isOpen"
      class="bg-white rounded-lg shadow-xl w-96 flex flex-col"
      style="height: 500px"
    >
      <!-- Header -->
      <div class="bg-primary-600 text-white p-4 rounded-t-lg flex justify-between items-center">
        <h3 class="font-semibold">HR Assistant</h3>
        <button @click="toggleChat" class="text-white hover:text-gray-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Messages -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="messageContainer">
        <div
          v-for="message in messages"
          :key="message.id"
          :class="[
            'max-w-[80%] rounded-lg p-3',
            message.isUser
              ? 'bg-primary-100 ml-auto'
              : 'bg-gray-100'
          ]"
        >
          {{ message.text }}
        </div>
      </div>

      <!-- Feedback (shown after bot response) -->
      <div v-if="showFeedback" class="p-3 bg-gray-50 border-t">
        <div class="text-sm text-gray-600 mb-2">Was this response helpful?</div>
        <div class="flex space-x-2">
          <button
            v-for="rating in [1, 2, 3, 4, 5]"
            :key="rating"
            @click="submitFeedback(rating)"
            class="p-1 hover:text-primary-600"
          >
            <svg
              class="w-5 h-5"
              :class="{ 'text-yellow-400': rating <= currentFeedback }"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Input -->
      <div class="p-4 border-t">
        <form @submit.prevent="sendMessage" class="flex space-x-2">
          <input
            v-model="newMessage"
            type="text"
            placeholder="Type your message..."
            class="flex-1 rounded-lg border border-gray-300 p-2 focus:outline-none focus:border-primary-500"
            :disabled="isLoading"
          />
          <button
            type="submit"
            class="bg-primary-600 text-white rounded-lg px-4 py-2 hover:bg-primary-700 disabled:opacity-50"
            :disabled="isLoading || !newMessage.trim()"
          >
            <svg
              v-if="isLoading"
              class="animate-spin h-5 w-5"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            <span v-else>Send</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'

const isOpen = ref(false)
const isLoading = ref(false)
const newMessage = ref('')
const messages = ref([])
const messageContainer = ref(null)
const showFeedback = ref(false)
const currentFeedback = ref(0)
const currentConversationId = ref(null)

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value && messages.value.length === 0) {
    // Add welcome message
    messages.value.push({
      id: Date.now(),
      text: 'Hello! I\'m your HR Assistant. How can I help you today?',
      isUser: false
    })
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (messageContainer.value) {
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || isLoading.value) return

  const messageText = newMessage.value
  newMessage.value = ''
  isLoading.value = true
  showFeedback.value = false

  // Add user message
  messages.value.push({
    id: Date.now(),
    text: messageText,
    isUser: true
  })

  await scrollToBottom()

  try {
    const response = await axios.post('/api/chatbot/message', {
      message: messageText,
      platform: 'web'
    })

    // Add bot response
    messages.value.push({
      id: Date.now(),
      text: response.data.response,
      isUser: false
    })

    currentConversationId.value = response.data.conversation_id
    showFeedback.value = true
    
    await scrollToBottom()
  } catch (error) {
    messages.value.push({
      id: Date.now(),
      text: 'Sorry, I encountered an error. Please try again.',
      isUser: false
    })
    console.error('Error:', error)
  } finally {
    isLoading.value = false
  }
}

const submitFeedback = async (rating) => {
  if (!currentConversationId.value) return

  try {
    currentFeedback.value = rating
    await axios.post('/api/chatbot/feedback', {
      conversation_id: currentConversationId.value,
      rating
    })
    showFeedback.value = false
  } catch (error) {
    console.error('Error submitting feedback:', error)
  }
}

onMounted(() => {
  // You can add any initialization logic here
})
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
