<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Recipe $recipe): RedirectResponse
    {
        // Check if the comments section is open for this recipe
        if (!$recipe->is_commentable) {
            return back()->with('error', 'Comments are closed for this recipe.');
        }

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

    // Deletes a comment
    // Only the comment owner can perform this action
    public function destroy(Comment $comment)
    {
        // Check if there is an authenticated user
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = Auth::user();

        // Authorization logic
        $isCommentAuthor = $user->id === $comment->user_id;
        $isRecipeAuthor = $comment->recipe && $user->id === $comment->recipe->user_id;
        $isAdmin = $user->role === 'admin'; // Adjust 'is_admin' to match your User model property

        if (!$isCommentAuthor && !$isRecipeAuthor && !$isAdmin) {
            abort(403, 'You do not have permission to delete this comment.');
        }

        $comment->delete();

        // Redirect back dynamically (works for both Recipe page and User Profile)
        return back()->with('success', 'Comment deleted successfully.');
    }
}
