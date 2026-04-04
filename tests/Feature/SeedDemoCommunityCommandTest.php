<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedDemoCommunityCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_community_command_creates_users_topics_and_replies(): void
    {
        $this->artisan('forum:seed-demo-community')
            ->assertSuccessful();

        $demoUsers = User::where('email', 'like', '%@forum-demo.test')->pluck('id');

        $this->assertSame(10, $demoUsers->count());
        $this->assertSame(15, Topic::whereIn('user_id', $demoUsers)->count());
        $this->assertSame(45, Reply::whereIn('user_id', $demoUsers)->count());
    }

    public function test_demo_community_command_is_idempotent(): void
    {
        $this->artisan('forum:seed-demo-community')->assertSuccessful();
        $this->artisan('forum:seed-demo-community')->assertSuccessful();

        $demoUsers = User::where('email', 'like', '%@forum-demo.test')->pluck('id');

        $this->assertSame(10, $demoUsers->count());
        $this->assertSame(15, Topic::whereIn('user_id', $demoUsers)->count());
        $this->assertSame(45, Reply::whereIn('user_id', $demoUsers)->count());
    }
}
