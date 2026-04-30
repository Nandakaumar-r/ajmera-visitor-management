<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Post::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,  // Assigns a random user to the post
            'type' => $this->faker->randomElement(['update', 'celebration', 'shoutout', 'project']),  // Example types
            'content' => $this->faker->sentence(10),  // Random content for the post
            'media_url' => $this->faker->optional()->imageUrl(),  // Optional media URL
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
