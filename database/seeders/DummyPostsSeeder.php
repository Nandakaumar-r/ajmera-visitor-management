<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class DummyPostsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // Get the first user as the author

        if (!$user) {
            return;
        }

        $posts = [
            [
                'content' => "Excited to share our new office space! 🏢 Looking forward to creating amazing memories here with our fantastic team. #NewBeginnings #OfficeLife",
                'media_url' => 'posts/office-1.jpg',
                'type' => 'update'
            ],
            [
                'content' => "Great team building session today! 🤝 Nothing beats collaborating with such talented individuals. #TeamWork #Growth",
                'media_url' => 'posts/team-1.jpg',
                'type' => 'celebration'
            ],
            [
                'content' => "Celebrating our team's success! 🎉 Proud of what we've achieved together this quarter. Here's to many more victories! #Success #Celebration",
                'media_url' => 'posts/celebration-1.jpg',
                'type' => 'celebration'
            ],
        ];

        foreach ($posts as $post) {
            Post::create([
                'user_id' => $user->id,
                'content' => $post['content'],
                'media_url' => $post['media_url'],
                'type' => $post['type']
            ]);
        }
    }
}
