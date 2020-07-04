<?php

namespace Tests\Traits;

use App\User;
use App\WorkPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ToUserRelationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_work_place_is_created_by_user()
    {
        $user = signin();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $workPlace->createdBy);
    }

    /** @test */
    public function a_work_place_is_updated_by_user()
    {
        $user = signin();
        $workPlace = factory(WorkPlace::class)->create(['updated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $workPlace->updatedBy);
    }

    /** @test */
    public function a_work_place_is_deleted_by_user()
    {
        $user = signin();
        $workPlace = factory(WorkPlace::class)->create(['deleted_by' => $user->id]);

        $this->assertInstanceOf(User::class, $workPlace->deletedBy);
    }
}
