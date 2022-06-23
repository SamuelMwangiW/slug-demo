<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * @test
     */
    public function it_respects_given_slug_when_creating_a_post()
    {
        $post = Post::factory()->create(['slug' => 'working-with-slugs-in-laravel']);

        $this->assertTrue($post->exists);

        $this->assertEquals('working-with-slugs-in-laravel', $post->slug);
    }

    /**
     * @test
     */
    public function it_adds_slug_when_creating_a_post()
    {
        $post = Post::factory()->create(['title' => 'Working with slugs in Laravel',]);

        $this->assertTrue($post->exists);

        $this->assertEquals('working-with-slugs-in-laravel', $post->slug);
    }

    /**
     * @test
     */
    public function it_adds_suffix_to_slug_if_a_conflict_exists()
    {
        $conflictingPost = Post::factory()->create(['title' => 'Working with slugs in Laravel']);

        $firstPost = Post::factory()->create(['title' => 'Working with slugs in Laravel',]);
        $secondPost = Post::factory()->create(['title' => 'Working with slugs in Laravel',]);
        $thirdPost = Post::factory()->create(['title' => 'Working with slugs in Laravel',]);

        $this->assertDatabaseCount('posts', 4);

        $this->assertEquals('working-with-slugs-in-laravel-1', $firstPost->slug);
        $this->assertEquals('working-with-slugs-in-laravel-2', $secondPost->slug);
        $this->assertEquals('working-with-slugs-in-laravel-3', $thirdPost->slug);
    }

    /**
     * @test
     */
    public function it_updates_post_with_provided_slug()
    {
        $post = Post::factory()->create();

        $post->update(['slug' => 'working-with-slugs-in-laravel']);

        $this->assertEquals('working-with-slugs-in-laravel', $post->slug);
    }

    /**
     * @test
     */
    public function update_generates_a_slug_when_slug_is_empty()
    {
        $post = Post::factory()->create();

        $post->update(['slug' => '']);

        $this->assertNotEmpty($post->slug);
    }

    /**
     * @test
     */
    public function update_generates_a_different_slug_when_updating_with_an_existing_slug()
    {
        Post::factory()->create(['slug' => 'existing-slug']);
        $post = Post::factory()->create();

        $post->update(['slug' => 'existing-slug']);

        $this->assertNotEquals('existing-slug', $post->slug);
    }
}
