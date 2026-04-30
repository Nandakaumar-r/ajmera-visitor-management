<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Embed\Embed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
            ->withCount('likes')
            ->latest()
            ->paginate(10);

        if (request()->ajax()) {
            return view('social-feed.partials.posts', compact('posts'));
        }

        return view('social-feed.index', compact('posts'));
    }

    public function loadMore(Request $request)
    {
        $page = $request->get('page', 1);
        
        $posts = Post::with(['user', 'comments.user'])
            ->withCount('likes')
            ->latest()
            ->paginate(10, ['*'], 'page', $page);

        return response()->view('social-feed.partials.posts', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'photo' => 'nullable|image|max:5120', // 5MB max
            'url' => 'nullable|url',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_image' => 'nullable|string',
        ]);

        $post = new Post();
        $post->user_id = auth()->id();
        $post->content = $validated['content'];
        $post->type = 'post';

        // Handle URL metadata
        if (!empty($validated['url'])) {
            $post->meta_url = $validated['url'];
            $post->meta_title = $validated['meta_title'] ?? null;
            $post->meta_description = $validated['meta_description'] ?? null;
            $post->meta_image = $validated['meta_image'] ?? null;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            if ($photo->isValid()) {
                $path = $photo->store('posts', 'public');
                $post->photo = $path;
            }
        }

        $post->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully!',
                'post' => view('social-feed.partials.single-post', ['post' => $post])->render()
            ]);
        }

        return back()->with('success', 'Post created successfully!');
    }

    public function fetchUrlMetadata(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $embed = new Embed();
            $info = $embed->get($request->url);

            if (!$info) {
                throw new \Exception('Failed to fetch URL metadata');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $info->title,
                    'description' => $info->description,
                    'image' => $info->image,
                    'url' => $request->url
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('URL metadata fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch URL metadata. Please check the URL and try again.'
            ], 422);
        }
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $post->delete();
        return redirect()->back()->with('success', 'Post deleted successfully!');
    }

    public function like(Post $post)
    {
        if (!$post->likedBy(auth()->user())) {
            $post->likes()->create([
                'user_id' => auth()->id()
            ]);
        }

        return response()->json([
            'success' => true,
            'liked' => true,
            'likes_count' => $post->likes()->count()
        ]);
    }

    public function unlike(Post $post)
    {
        $post->likes()->where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'liked' => false,
            'likes_count' => $post->likes()->count()
        ]);
    }
}
