<?php

namespace Tests\Feature\Permissions;

use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_shift_cannot_be_added_by_user_without_permissions_to_edit()
    {
        $items = createWorkerAndWorkPlace();
        $shift = factory(Shift::class)->raw([
            'work_place_id' => $items['workPlace']->id,
            'worker_id' => $items['worker']->id,
        ]);
        signIn();

        $response = $this->json('post', 'api/shift', $shift);

        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertCount(0, Shift::all());
    }

    /** @test */
    public function a_shift_cannot_be_updated_by_user_without_permissions_to_edit()
    {
        $items = createWorkerAndWorkPlace();
        $shift = factory(Shift::class)->create([
            'work_place_id' => $items['workPlace']->id,
            'worker_id' => $items['worker']->id,
        ]);
        signIn();

        $dataToUpdateShift = factory(Shift::class)->raw();
        $response = $this->json('put', 'api/shift/' .  $shift->id, $dataToUpdateShift);

        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertEquals($shift->start, Shift::first()->start);
    }
    
    /** @test */
    public function a_shift_cannot_be_deleted_by_user_without_permissions_to_edit()
    {
        $this->withoutExceptionHandling();
        $items = createWorkerAndWorkPlace();
        $shift = factory(Shift::class)->create([
            'work_place_id' => $items['workPlace']->id,
            'worker_id' => $items['worker']->id,
        ]);
        signIn();

        $response = $this->json('delete', 'api/shift/' .  $shift->id);

        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertCount(1, Shift::all());
    }
    
    /** @test */
    public function a_work_place_can_not_be_deleted_by_user_that_isnt_its_creator()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $response = $this->json('delete', '/api/work_place/' . $permissions['workPlace']->id);

        $response->assertStatus(403);
        $this->assertCount(1, WorkPlace::all());
    }

    /** @test */
    public function a_work_place_can_be_deleted_by_user_that_is_its_creator()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['creator_of_work_place']);

        $response = $this->json('delete', '/api/work_place/' . $permissions['workPlace']->id);

        $this->assertCount(0, WorkPlace::all());
        $response->assertStatus(200);
    }
}
