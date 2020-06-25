<?php

namespace Tests;

use App\Worker;
use App\WorkPlace;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkPlaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_worker_place_is_created_by_user()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $workPlace->createdBy);
    }

    /** @test */
    public function a_work_place_can_have_many_workers()
    {
        $workPlace = factory(WorkPlace::class)->create();
        factory(Worker::class, 5)->create(['work_place_id' => $workPlace->id]);

        $this->assertEquals(5, $workPlace->workers->count());
        $workPlace->workers->each(function ($worker) {
            $this->assertInstanceOf(Worker::class, $worker);
        });
    }
}
