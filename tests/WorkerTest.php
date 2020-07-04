<?php

namespace Tests;

use App\Worker;
use App\WorkPlace;
use App\User;
use App\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_worker_is_assigned_to_work_place()
    {
        $workPlace = factory(WorkPlace::class)->create();
        factory(Worker::class)->create([
            'work_place_id' => $workPlace->id,
        ]);

        $workersWorkPlace = Worker::first()->workPlace;

        $this->assertInstanceOf(WorkPlace::class, $workersWorkPlace);
    }
    
    /** @test */
    public function a_worker_is_created_by_user()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create([
            'created_by' => $user->id,
        ]);
        $worker = factory(Worker::class)->create([
            'created_by' => $user->id,
            'work_place_id' => $workPlace->id,
        ]);

        $this->assertInstanceOf(User::class, $worker->createdBy);
    }

    /** @test */
    public function a_worker_can_belong_to_user()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create();
        $worker = factory(Worker::class)->create([
            'user_id' => $user->id,
            'work_place_id' => $workPlace->id,
        ]);

        $this->assertInstanceOf(User::class, $worker->belongsToUser);
    }

    /** @test */
    public function a_worker_can_have_many_shifts()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create();
        $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id]);
        $shift = factory(Shift::class, 5)->create([
            'work_place_id' => $workPlace->id,
            'worker_id' => $worker->id,
        ]);

        $this->assertEquals(5, $worker->shifts->count());
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $worker->shifts);
        $worker->shifts->each(function ($shift) {
            $this->assertInstanceOf(Shift::class, $shift);
        });
    }
}
