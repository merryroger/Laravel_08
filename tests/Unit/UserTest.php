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

    public function testLastTwoRecordsAreInactive()
    {
        // Statement 1.1: all (last two) records are inactive
        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testTheLastRecordByUser1IsActivated()
    {
        // Statement 1.1: the last record is being activated
        $this->User1->setLastPostActive();

        // Statement 1.2: the last record is active, the previous record is inactive
        $lastPost = $this->User1->getPostsReversed(1)->first();
        $this->assertTrue($lastPost->isActive());

        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(4, $inactivePosts);
    }

    public function testLastTwoRecordsByUser1AreActivated()
    {
        $this->User1->setLastPostActive();
        $this->User1->setLastPostActive();

        $lastPosts = $this->User1->getPostsReversed(2);

        // Statement 1.2: the previous record is being activated
        /* $this->assertTrue($lastPosts->first()->isActive()); */
        $this->assertTrue($lastPosts->last()->isActive());

        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(3, $inactivePosts);
    }

    public function testActivationMethodHasNotAffectOnAnotherUsersRecords()
    {
        $this->User1->setLastPostActive();

        // Statement 1.3: the tested method has not affect on another user`s records
        $inactivePosts = $this->User2->getInactivePosts()->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testDeletionMethodHasNotAffectOnAnotherUsersRecords()
    {
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        // Statement 2.1: the tested method has not affect on another user`s records
        $inactivePosts = $this->User2->getInactivePosts()->count();
        $this->assertEquals(5, $inactivePosts);
    }

    public function testDeletionMethodRemovesInactivePosts()
    {
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        // Statement 2.2: the method removes inactive records
        $inactivePosts = $this->User1->getInactivePosts()->count();
        $this->assertEquals(0, $inactivePosts);

    }

    public function testDeletionMethodKeepsActivePosts()
    {
        $this->User1->setLastPostActive();
        $this->User1->deleteInactivePosts();

        // Statement 2.3: the method keeps active records
        $activePosts = $this->User1->getActivePosts()->count();
        $this->assertEquals(1, $activePosts);

    }

}
