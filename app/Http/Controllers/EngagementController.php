<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Events\PostLiked;
use App\Events\PostCommented;

class EngagementController extends Controller
{
    // Display all posts with sorting options
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'most_recent');

        $posts = Post::with(['user', 'likes', 'comments'])
            ->when($sort === 'trending', fn($query) => $query->trending())
            ->when($sort === 'most_liked', fn($query) => $query->withCount('likes')->orderBy('likes_count', 'desc'))
            ->when($sort === 'most_recent', fn($query) => $query->mostRecent())
            ->get();

        return view('engagement.index', compact('posts'));
    }

    // Create a new post
    public function store(Request $request)
    {
        $post = Post::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'content' => $request->content,
            'media_url' => $request->media_url,
        ]);

        return back()->with('status', 'Post created successfully!');
    }

    // Like a post
    public function like(Post $post)
    {
        $like = Like::firstOrCreate([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
        ]);

        event(new PostLiked($post, auth()->user()));

        return response()->json(['likes_count' => $post->likes->count()]);
    }

    // Comment on a post
    public function comment(Request $request, Post $post)
    {
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        event(new PostCommented($post, auth()->user()));

        return response()->json(['comments_count' => $post->comments->count()]);
    }
}
