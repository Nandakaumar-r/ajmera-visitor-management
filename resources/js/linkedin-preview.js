document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url');
    const fetchPreviewBtn = document.getElementById('fetch-preview');
    const previewContainer = document.getElementById('url-preview');
    const form = document.querySelector('form');
    const hiddenInputsContainer = document.createElement('div');
    form.appendChild(hiddenInputsContainer);

    fetchPreviewBtn?.addEventListener('click', async function() {
        const url = urlInput.value.trim();
        if (!url) return;

        try {
            fetchPreviewBtn.disabled = true;
            fetchPreviewBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            `;

            const response = await fetch('/posts/fetch-url', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url: url })
            });

            const data = await response.json();

            if (data.success) {
                // Clear previous hidden inputs
                hiddenInputsContainer.innerHTML = '';

                // Add hidden inputs for metadata
                const hiddenInputs = `
                    <input type="hidden" name="url" value="${url}">
                    <input type="hidden" name="meta_title" value="${data.data.title || ''}">
                    <input type="hidden" name="meta_description" value="${data.data.description || ''}">
                    <input type="hidden" name="meta_image" value="${data.data.image || ''}">
                `;
                hiddenInputsContainer.innerHTML = hiddenInputs;

                // Show preview
                previewContainer.innerHTML = `
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                        ${data.data.image ? `
                            <img src="${data.data.image}" alt="${data.data.title}" class="w-full h-48 object-cover">
                        ` : ''}
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 dark:text-white">${data.data.title || 'No title available'}</h3>
                            ${data.data.description ? `
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${data.data.description}</p>
                            ` : ''}
                            <p class="mt-1 text-xs text-gray-400">${url}</p>
                        </div>
                    </div>
                `;
                previewContainer.classList.remove('hidden');
            } else {
                throw new Error(data.message || 'Failed to fetch preview');
            }
        } catch (error) {
            console.error('Preview error:', error);
            alert('Failed to fetch URL preview. Please check the URL and try again.');
        } finally {
            fetchPreviewBtn.disabled = false;
            fetchPreviewBtn.innerHTML = 'Preview';
        }
    });

    // Clear preview when URL is cleared
    urlInput?.addEventListener('input', function() {
        if (!this.value.trim()) {
            previewContainer.innerHTML = '';
            previewContainer.classList.add('hidden');
            hiddenInputsContainer.innerHTML = '';
        }
    });
});
