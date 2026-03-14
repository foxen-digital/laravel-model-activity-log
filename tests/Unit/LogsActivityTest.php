<?php

use Foxen\LaravelModelActivityLog\Models\Activity;
use Foxen\LaravelModelActivityLog\Tests\Fixtures\Post;
use Foxen\LaravelModelActivityLog\Tests\Fixtures\SoftDeletingPost;
use Foxen\LaravelModelActivityLog\Tests\Fixtures\User;
use Foxen\LaravelModelActivityLog\Tests\TestCase;

use function Pest\Laravel\actingAs;

uses(TestCase::class);

it('logs when a model is created', function () {
    $post = Post::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $activity = Activity::first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($post->id);
    expect($activity->event)->toBe('created');
});

it('logs when a model is updated', function () {
    $post = Post::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $post->update([
        'title' => 'New Title',
    ]);

    $activity = Activity::where('event', 'updated')->first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($post->id);
    expect($activity->properties['old']['title'])->toBe('Test Title');
    expect($activity->properties['new']['title'])->toBe('New Title');
});

it('logs when a model is deleted', function () {
    $post = Post::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $postId = $post->id;

    $post->delete();

    $activity = Activity::where('event', 'deleted')->first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($postId);
});

it('logs the causer of the activity', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->fresh();

    actingAs($user);

    $post = Post::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $activity = Activity::whereSubject($post)->where('event', 'created')->first();

    expect($activity->causer_id)->toBe($user->id);
    expect($activity->causer_type)->toBe(User::class);
});

it('does not log ignored attributes', function () {
    $post = Post::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $post->update([
        'title' => 'New Title',
        'updated_at' => now()->addDay(),
    ]);

    $activity = Activity::where('event', 'updated')->first();

    expect($activity->properties['new'])->not->toHaveKey('updated_at');
});

it('redacts redacted attributes', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->update([
        'password' => bcrypt('new-password'),
    ]);

    $activity = Activity::where('event', 'updated')->first();

    expect($activity->properties['new']['password'])->toBe('[REDACTED]');
});

it('logs a soft deleted model', function () {
    $post = SoftDeletingPost::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $postId = $post->id;
    $post->delete();

    $activity = Activity::whereSubject($post)->where('event', 'deleted')->first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($postId);
});

it('logs when a soft deleted model is restored', function () {
    $post = SoftDeletingPost::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $post->delete();
    $post->restore();

    $activity = Activity::whereSubject($post)->where('event', 'restored')->first();

    expect($activity)->not->toBeNull();
    expect($activity->subject_id)->toBe($post->id);
});

it('uses system as actor when no user is authenticated', function () {
    $post = Post::create([
        'title' => 'Test Title',
        'body' => 'Test Body',
    ]);

    $activity = Activity::whereSubject($post)->where('event', 'created')->first();

    expect($activity->causer_id)->toBeNull();
    expect($activity->causer_type)->toBeNull();
    expect($activity->description)->toContain('System');
});

it('uses the model activityLogName when set', function () {
    $post = new class extends Post
    {
        protected $activityLogName = 'custom-log';

        protected $table = 'posts';
    };

    $post->title = 'Test Title';
    $post->body = 'Test Body';
    $post->save();

    $activity = Activity::where('log_name', 'custom-log')->first();

    expect($activity)->not->toBeNull();
    expect($activity->log_name)->toBe('custom-log');
});
