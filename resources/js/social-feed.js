// Make functions globally accessible
window.toggleLike = toggleLike;
window.toggleComments = toggleComments;
window.submitComment = submitComment;
window.deleteComment = deleteComment;
window.loadMorePosts = loadMorePosts;

// Like functionality
async function toggleLike(postId) {
    try {
        const button = document.querySelector(`.like-button[data-post-id="${postId}"]`);
        if (!button) return;

        const countSpan = button.querySelector('.likes-count');
        const icon = button.querySelector('svg');
        
        const isLiked = button.classList.contains('text-indigo-600');
        const url = `/posts/${postId}/${isLiked ? 'unlike' : 'like'}`;
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        // Update like count
        if (countSpan) {
            countSpan.textContent = data.likes_count;
        }
        
        // Update button appearance
        if (data.liked) {
            button.classList.add('text-indigo-600', 'dark:text-indigo-400');
            icon.setAttribute('fill', 'currentColor');
        } else {
            button.classList.remove('text-indigo-600', 'dark:text-indigo-400');
            icon.setAttribute('fill', 'none');
        }
    } catch (error) {
        console.error('Error toggling like:', error);
        alert('Failed to update like status. Please try again.');
    }
}

// Comment functionality
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection) {
        commentsSection.classList.toggle('hidden');
    }
}

// Show reply form for a comment
function showReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        replyForm.classList.remove('hidden');
        const textarea = replyForm.querySelector('textarea');
        if (textarea) {
            textarea.focus();
        }
    }
}

// Hide reply form for a comment
function hideReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        replyForm.classList.add('hidden');
        const textarea = replyForm.querySelector('textarea');
        if (textarea) {
            textarea.value = '';
        }
    }
}

async function submitComment(event, postId, parentId = null) {
    event.preventDefault();
    
    const form = event.target;
    const textarea = form.querySelector('textarea[name="comment"]');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Get the comment value directly from the textarea
    const comment = textarea.value.trim();
    console.log('Form:', {
        form: form,
        textarea: textarea,
        commentValue: comment,
        postId: postId,
        parentId: parentId
    });
    
    if (!comment) {
        console.log('Comment is empty');
        return;
    }
    
    // Disable form while submitting
    textarea.disabled = true;
    submitButton.disabled = true;
    
    try {
        const requestData = {
            post_id: postId,
            comment: comment,
            parent_id: parentId
        };
        console.log('Sending request:', requestData);
        
        const response = await fetch('/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        });

        const responseData = await response.json();
        console.log('Response:', {
            status: response.status,
            data: responseData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // If this was a reply, hide the reply form
        if (parentId) {
            hideReplyForm(parentId);
        }

        // Refresh the comments section
        const commentsSection = document.getElementById(`comments-${postId}`);
        if (commentsSection) {
            // Make the comments section visible if it was hidden
            commentsSection.classList.remove('hidden');
            
            // Reload the comments section
            const response = await fetch(`/posts/${postId}/comments`);
            const html = await response.text();
            const commentsContainer = commentsSection.querySelector('.comments-container');
            if (commentsContainer) {
                commentsContainer.innerHTML = html;
            }
        }
        
        // Clear the textarea
        textarea.value = '';
    } catch (error) {
        console.error('Error submitting comment:', error);
        alert('Failed to submit comment. Please try again.');
    } finally {
        // Re-enable form
        textarea.disabled = false;
        submitButton.disabled = false;
    }
}

async function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) return;
    
    try {
        const response = await fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            // Remove the comment element
            const commentElement = document.getElementById(`comment-${commentId}`);
            if (commentElement) {
                commentElement.remove();
            }
            
            // Update comment count
            const commentCount = document.querySelector(`.comment-button[data-post-id="${data.post_id}"] .comments-count`);
            if (commentCount) {
                commentCount.textContent = data.comments_count;
            }
            
            // Hide comments section if no more comments
            if (data.comments_count === 0) {
                const commentsSection = document.getElementById(`comments-${data.post_id}`);
                if (commentsSection) {
                    commentsSection.classList.add('hidden');
                }
            }
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        alert('Failed to delete comment. Please try again.');
    }
}

// Infinite scroll functionality
let page = 1;
let loading = false;
let hasMorePosts = true;

async function loadMorePosts() {
    if (loading || !hasMorePosts) return;
    
    loading = true;
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
    }
    
    try {
        const response = await fetch(`/social-feed/load-more?page=${page + 1}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const html = await response.text();
        if (html.trim()) {
            const postsContainer = document.getElementById('posts-container');
            if (postsContainer) {
                postsContainer.insertAdjacentHTML('beforeend', html);
                
                // Initialize lazy loading for new images
                const newLazyImages = postsContainer.querySelectorAll('img[data-src]');
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    });
                });
                newLazyImages.forEach(img => imageObserver.observe(img));
            }
            page++;
        } else {
            hasMorePosts = false;
            const noMorePosts = document.getElementById('no-more-posts');
            if (noMorePosts) {
                noMorePosts.classList.remove('hidden');
            }
        }
    } catch (error) {
        console.error('Error loading more posts:', error);
    } finally {
        loading = false;
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
    }
}

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));

    // Detect when user is near bottom of page
    window.addEventListener('scroll', () => {
        const scrollPosition = window.innerHeight + window.scrollY;
        const documentHeight = document.documentElement.scrollHeight;
        
        if (scrollPosition >= documentHeight - 1000) { // Load more when within 1000px of bottom
            loadMorePosts();
        }
    });
});
