<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Post;
use App\User;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $postActive;
    protected $postNotActive;

    public function setUp()
    {
        parent::setUp();
        $this->postNotActive = factory(Post::class)->create();
        $this->postActive = factory(Post::class)->states('active')->create();
    }

    public function testUser()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $user->id]);
        $userFromDB = User::find($user->id);
        $this->assertEquals($userFromDB, $post->user);
    }

    public function testIsNotActiveTrue()
    {
        $this->assertTrue($this->postNotActive->isNotActive());
    }

    public function testIsNotActiveFalse()
    {
        $this->assertFalse($this->postActive->isNotActive());
    }

    public function testIsActiveTrue()
    {
        $this->assertTrue($this->postActive->isActive());
    }

    public function testIsActiveFalse()
    {
        $this->assertFalse($this->postNotActive->isActive());
    }

    public function testSetActive()
    {
        $this->postNotActive->setActive();
        $postFromDB = Post::find($this->postNotActive->id);
        $this->assertTrue($postFromDB->isActive());
    }
}
