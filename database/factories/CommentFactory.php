<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'user_id' => User::factory(),
            'guest_name' => null,
            'content' => fake()->randomElement([
                'Amazing recipe, made it yesterday and it turned out delicious!',
                'I added a bit more garlic and it was even better.',
                'Simple and tasty, I will definitely make this again.',
                'The whole family loved it, thank you for sharing!',
                'Perfect for a quick weeknight dinner, highly recommend.',
                'I tweaked it slightly but the result was fantastic.',
                'This became my go-to recipe, absolutely wonderful.',
                'Made this for a dinner party and everyone asked for the recipe!',
                'This recipe is amazing! I added some extra garlic.',
                'Perfect for a quick dinner. My kids loved it!',
                'Can I replace the milk with a plant-based alternative?',
                'Delicious! Made it yesterday and it was a hit.',
                'The instructions are very clear, thank you!',
                'A bit too salty for my taste, but otherwise great.',
            ]),
            'parent_id' => null,
            'created_at' => now(),
        ];
    }

    // State: reply to an existing comment
    public function isReply(Comment $parent): static {
        return $this->state([
            'parent_id'=> $parent->id,
            'recipe_id'=> $parent->recipe_id,
        ]);
    }

    // State: comment from a guest user
    public function asGuest(?string $name = null): static {
        return $this->state([
            'user_id' => null,
            'guest_name' => $name ?? fake()->firstName(),
        ]);
    }

    // State: comment for a specific recipe
    public function forRecipe(Recipe $recipe): static {
        return $this->state(['recipe_id'=> $recipe->id]);
    }

    // State: comment from user
    public function forUser(User $user): static {
        return $this->state([
            'user_id' => $user->id,
            'guest_name' => null,
        ]);
    }
}
