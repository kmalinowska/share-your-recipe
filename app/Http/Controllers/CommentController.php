<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'guest_name' => Auth::check() ? 'nullable' : 'required|string|max:50',
        ]);

        $comment = new Comment();
        $comment->recipe_id = $recipe->id;
        $comment->content = $validated['content'];
        $comment->parent_id = $validated['parent_id'] ?? null;

        if (Auth::check()) {
            $comment->user_id = Auth::id();
        } else {
            $comment->guest_name = $validated['guest_name'];
        }

        $comment->save();

        return back()->with('success', 'Comment added successfully!');
    }
}
