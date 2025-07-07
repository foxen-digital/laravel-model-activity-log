<?php

use Foxen\LaravelModelActivityLog\Models\Activity;
use Foxen\LaravelModelActivityLog\Tests\Fixtures\Post;
use Foxen\LaravelModelActivityLog\Tests\Fixtures\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;

uses(\Foxen\LaravelModelActivityLog\Tests\TestCase::class);

it("logs when a model is created", function () {
    $post = Post::create([
        "title" => "Test Title",
        "body" => "Test Body",
    ]);

    $activity = Activity::first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($post->id);
    expect($activity->event)->toBe("created");
});

it("logs when a model is updated", function () {
    $post = Post::create([
        "title" => "Test Title",
        "body" => "Test Body",
    ]);

    $post->update([
        "title" => "New Title",
    ]);

    $activity = Activity::where("event", "updated")->first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($post->id);
    expect($activity->properties["old"]["title"])->toBe("Test Title");
    expect($activity->properties["new"]["title"])->toBe("New Title");
});

it("logs when a model is deleted", function () {
    $post = Post::create([
        "title" => "Test Title",
        "body" => "Test Body",
    ]);

    $postId = $post->id;

    $post->delete();

    $activity = Activity::where("event", "deleted")->first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($postId);
});

it("logs the causer of the activity", function () {
    $user = User::create([
        "name" => "Test User",
        "email" => "test@example.com",
        "password" => bcrypt("password"),
    ]);

    $user->fresh();

    actingAs($user);

    $post = Post::create([
        "title" => "Test Title",
        "body" => "Test Body",
    ]);

    $activity = Activity::find(2); // First activity is user creation...

    expect($activity->causer_id)->toBe($user->id);
    expect($activity->causer_type)->toBe(User::class);
});

it("does not log ignored attributes", function () {
    $post = Post::create([
        "title" => "Test Title",
        "body" => "Test Body",
    ]);

    $post->update([
        "title" => "New Title",
        "updated_at" => now()->addDay(),
    ]);

    $activity = Activity::where("event", "updated")->first();

    expect($activity->properties["new"])->not->toHaveKey("updated_at");
});

it("redacts redacted attributes", function () {
    $user = User::create([
        "name" => "Test User",
        "email" => "test@example.com",
        "password" => bcrypt("password"),
    ]);

    $user->update([
        "password" => bcrypt("new-password"),
    ]);

    $activity = Activity::where("event", "updated")->first();

    expect($activity->properties["new"]["password"])->toBe("[REDACTED]");
});
