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

    public function testGetPostsReversed()
    {
        $posts = $this->User1->getPostsReversed(5);
        for ($i = 5; $i > 0; $i--) {
            $this->assertEquals($i, $posts[5 - $i]->id);
        }
    }

    public function testGetInactivePosts()
    {
        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testGetActivePosts()
    {
        $this->User1->setLastPostActive();
        $this->User1->setLastPostActive();

        $activePosts = $this->User1->getActivePosts()->count();
        $this->assertEquals(2, $activePosts);
    }

    public function testLastTwoRecordsAreInactive()
    {
        $lastPosts = $this->User1->getPostsReversed(2);
        foreach ($lastPosts as $post) {
            $this->assertFalse($post->isActive());
        }
    }

    public function testSetLastPostActive()
    {
        $this->User1->setLastPostActive();

        $lastPost = $this->User1->getPostsReversed(1)->first();
        $this->assertTrue($lastPost->isActive());

        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(4, $inactivePosts);
    }

    public function testSetLastPostActiveActivatesLastTwoRecords()
    {
        $this->User1->setLastPostActive();
        $this->User1->setLastPostActive();

        $lastPosts = $this->User1->getPostsReversed(2);

        foreach ($lastPosts as $post) {
            $this->assertTrue($post->isActive());
        }

        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(3, $inactivePosts);
    }

    public function testSetLastPostActiveHasNotAffectOnAnotherUsersRecords()
    {
        $this->User1->setLastPostActive();

        $inactivePosts = $this->User2->getInactivePosts()->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testDeleteInactivePostsHasNotAffectOnAnotherUsersRecords()
    {
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        $inactivePosts = $this->User2->getInactivePosts()->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testDeleteInactivePosts()
    {
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(0, $inactivePosts);

    }

    public function testDeleteInactivePostsKeepsActivePosts()
    {
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        $activePosts = $this->User1->getActivePosts()->count();
        $this->assertEquals(1, $activePosts);

    }

}
