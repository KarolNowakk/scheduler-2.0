<?php

namespace Tests\Feature;

use App\User;
use App\WorkPlace;
use App\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp() : void
    {
        parent::setUp();
    }
    
    /** @test */
    public function permission_is_assigned_to_work_place()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $user->id]);
        $permission = factory(Permission::class)->create([
            'user_id' =>  $user->id,
            'work_place_id' => $workPlace->id,
        ]);

        $this->assertTrue($permission->workPlace instanceof WorkPlace);
    }

    /** @test */
    public function permission_is_assigned_to_user()
    {
        $user = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $user->id]);
        $permission = factory(Permission::class)->create([
            'user_id' =>  $user->id,
            'work_place_id' => $workPlace->id,
        ]);

        $this->assertTrue($permission->user instanceof User);
    }
}
