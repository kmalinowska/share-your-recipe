<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|uuid|exists:comments,id',
            'guest_name' => Auth::check() ? 'nullable' : 'required|string|max:100',
        ]);

        // Flat threading — if parent_id exists, find the root comment.
        // This ensures all replies are stored at depth = 1,
        // never creating deeper nesting in the database.
        $parentId = null;
        if (!empty($validated['parent_id'])) {
            $parent = Comment::find($validated['parent_id']);
            // If the parent itself is a reply (has parent_id),
            // use the grandparent as the parent → flat structure
            $parentId = $parent?->parent_id ?? $parent?->id;
        }

        $comment = new Comment();
        $comment->recipe_id = $recipe->id;
        $comment->content = $validated['content'];
        $comment->parent_id = $parentId;

        if (Auth::check()) {
            $comment->user_id = Auth::id();
        } else {
            $comment->guest_name = $validated['guest_name'];
        }

        $comment->save();

        return back()->with('success', 'Comment added successfully!');
    }
}
