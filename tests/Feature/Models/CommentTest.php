<?php

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can be authored by a user or a guest', function () {
    $userComment = Comment::factory()->create(['guest_name' => null]);
    $guestComment = Comment::factory()->asGuest('Anna')->create();

    expect($userComment->user)->toBeInstanceOf(User::class)
        ->and($guestComment->user)->toBeNull()
        ->and($guestComment->guest_name)->toBe('Anna');
});

it('returns the correct author name via attribute', function () {
    $user = User::factory()->create(['name' => 'Jan Kowalski']);
    $userComment = Comment::factory()->forUser($user)->create();
    $guestComment = Comment::factory()->asGuest('Beata')->create();
    $anonComment = Comment::factory()->create(['user_id' => null, 'guest_name' => null]);

    expect($userComment->author_name)->toBe('Jan Kowalski')
        ->and($guestComment->author_name)->toBe('Beata')
        ->and($anonComment->author_name)->toBe('Anonim');
});

it('can have replies (parent-child relationship)', function () {
    $parent = Comment::factory()->create();
    $reply = Comment::factory()->isReply($parent)->create();

    expect($parent->replies)->toHaveCount(1)
        ->and($reply->parent->id)->toBe($parent->id)
        ->and($parent->replies->first()->id)->toBe($reply->id);
});
