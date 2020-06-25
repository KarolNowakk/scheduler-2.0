<?php

namespace Tests;

use App\Worker;
use App\WorkPlace;
use App\User;
use App\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_shift_is_assigned_to_work_place()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $user->id]);
        $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id]);
        $shift = factory(Shift::class)->create([
            'worker_id' => $worker->id,
            'work_place_id' => $workPlace->id,
        ]);

        $this->assertInstanceOf(WorkPlace::class, $shift->workPlace);
    }

    /** @test */
    public function a_shift_is_created_by_user()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $user->id]);
        $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id]);
        $shift = factory(Shift::class)->create([
            'worker_id' => $worker->id,
            'work_place_id' => $workPlace->id,
        ]);
        $this->assertInstanceOf(User::class, $workPlace->createdBy);
    }
}
