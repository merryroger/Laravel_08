<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Post;

class UserTest extends TestCase
{

    use RefreshDatabase;

    protected $User1, $User2;
    protected $Posts1, $Posts2;

    public function setUp()
    {
        parent::setUp();
        $this->User1 = factory(User::class)->create();
        $this->User2 = factory(User::class)->create();
        factory(Post::class, 5)->create(['user_id' => $this->User1->id]);
        factory(Post::class, 5)->create(['user_id' => $this->User2->id]);
    }

    public function testSetHasInactiveLastRecords()
    {
        $posts = Post::where('user_id', $this->User1->id)
            ->orderBy('id', 'DESC')
            ->skip(0)
            ->take(2)
            ->get();

        $this->assertTrue($posts->first()->isNotActive());
        $this->assertTrue($posts->last()->isNotActive());

    }

    public function testActivateLastRecord()
    {
        $this->User1->setLastPostActive();

        $posts = Post::where('user_id', $this->User1->id)
            ->orderBy('id', 'DESC')
            ->skip(0)
            ->take(2)
            ->get();

        $this->assertTrue($posts->first()->isActive());
        $this->assertTrue($posts->last()->isNotActive());

        $inactivePosts = Post::where('user_id', $this->User1->id)->where('status_id', 0)->count();
        $this->assertEquals(4, $inactivePosts);

    }

    public function testActivatePreviousRecord()
    {
        $this->User1->setLastPostActive();
        $this->User1->setLastPostActive();

        $posts = Post::where('user_id', $this->User1->id)
            ->orderBy('id', 'DESC')
            ->skip(0)
            ->take(2)
            ->get();

        $this->assertTrue($posts->first()->isActive());
        $this->assertTrue($posts->last()->isActive());

        $inactivePosts = Post::where('user_id', $this->User1->id)->where('status_id', 0)->count();
        $this->assertEquals(3, $inactivePosts);

        $inactivePosts = Post::where('user_id', $this->User2->id)->where('status_id', 0)->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testInactivePostsDeletion()
    {
        $this->User1->setLastPostActive();
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        $inactivePosts = Post::where('user_id', $this->User1->id)->where('status_id', 0)->count();
        $this->assertEquals(0, $inactivePosts);

        $inactivePosts = Post::where('user_id', $this->User1->id)->where('status_id', 1)->count();
        $this->assertEquals(2, $inactivePosts);

        $inactivePosts = Post::where('user_id', $this->User2->id)->where('status_id', 0)->count();
        $this->assertEquals(5, $inactivePosts);
    }
}
