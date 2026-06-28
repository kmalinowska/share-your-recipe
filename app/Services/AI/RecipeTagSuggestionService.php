<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class RecipeTagSuggestionService
{
    public function suggest(
        string $title,
        string $category,
        array $ingredients,
        array $steps,
        array $availableTags
    ): array {

        $ingredientNames = collect($ingredients)
            ->pluck('name')
            ->implode(', ');

        $stepsText = implode("\n", $steps);

        $tags = implode(', ', $availableTags);

        $prompt = <<<PROMPT
You are an assistant that suggests recipe tags.

The user already selected the recipe category.
Treat it as correct.

Recipe title:
{$title}

Category:
{$category}

Ingredients:
{$ingredientNames}

Preparation:
{$stepsText}

Available tags:
{$tags}

Rules:

- Choose ONLY tags from the available list.
- Return maximum 5 tags.
- Return ONLY valid JSON.
- Do not explain anything.
- Do not invent new tags.

Example:

["Italian","Dinner","Quick"]

PROMPT;

        $response = Http::withToken(
            config('services.mistral.api_key')
        )
            ->acceptJson()
            ->post(
                'https://api.mistral.ai/v1/chat/completions',
                [
                    'model' => config('services.mistral.model'),

                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ]
                    ],

                    'temperature' => 0.2,
                ]
            );

        $response->throw();

        $content = $response->json(
            'choices.0.message.content'
        );

        $decoded = json_decode(
            $content,
            true
        );

        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }
}
