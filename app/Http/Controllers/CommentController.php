<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|max:500'
        ]);

        $comment = new Comment();
        $comment->user_id = auth()->id();
        $comment->post_id = $validated['post_id'];
        $comment->comment = $validated['comment'];
        $comment->save();

        $post = Post::find($validated['post_id']);

        return response()->json([
            'success' => true,
            'id' => $comment->id,
            'comment' => $comment->comment,
            'user_name' => $comment->user->name,
            'user_initial' => substr($comment->user->name, 0, 1),
            'created_at' => $comment->created_at->diffForHumans(),
            'comments_count' => $post->comments()->count()
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post = $comment->post;
        $comment->delete();

        return response()->json([
            'success' => true,
            'post_id' => $post->id,
            'comments_count' => $post->comments()->count()
        ]);
    }
}
