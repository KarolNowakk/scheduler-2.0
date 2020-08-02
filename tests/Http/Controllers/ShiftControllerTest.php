<?php

namespace Tests\Http\Controllers;

use App\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ShiftControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_shift_can_be_added_to_schedule()
    {
        $firstUser = signIn();
        $permissions = setUpPermissionForUser();
        $shift = getShiftAndCreateWorker($permissions);
        $firstUser->tokens()->each(fn ($token) => $token->delete());

        signIn($permissions['user']);
        $response = $this->json('post', 'api/shift', $shift['shift']);

        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(1, Shift::all());
    }

    /** @test */
    public function a_shift_can_be_updated()
    {
        $this->withoutExceptionHandling();
        $permissions = setUpPermissionForUser();
        $shiftAndWorker = createShiftAndWorker($permissions);

        signIn($permissions['user']);
        $response = $this->json('put', 'api/shift/' . $shiftAndWorker['shift']->id, [
            'shift_start' => '12:00',
            'shift_end' => '22:00',
        ]);

        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertEquals(Shift::first()->shift_end, '22:00');
        $this->assertEquals(Shift::first()->shift_start, '12:00');
    }

    /** @test */
    public function a_shift_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $permission = setUpPermissionForUser();
        createShiftAndWorker($permission);

        signIn($permission['user']);

        $this->assertCount(1, Shift::all());

        $this->delete('api/shift/1');

        $this->assertCount(0, Shift::all());
    }

    /** @test */
    public function all_fields_are_required_when_adding_shift()
    {
        $permission = setUpPermissionForUser();
        createShiftAndWorker($permission);

        signIn($permission['user']);

        $response = $this->post('api/shift', [
            'worker_id' => null,
            'work_place_id' => null,
            'day' => null,
            'shift_start' => null,
            'shift_end' => null,
        ]);

        $response->assertSessionHasErrors('worker_id');
        $response->assertSessionHasErrors('work_place_id');
        $response->assertSessionHasErrors('day');
        $response->assertSessionHasErrors('shift_start');
        $response->assertSessionHasErrors('shift_end');
    }

    /** @test */
    public function worker_id_and_work_place_id_must_be_numeric_when_adding_and_updating()
    {
        $permission = setUpPermissionForUser();
        createShiftAndWorker($permission);

        signIn($permission['user']);

        $response = $this->post('api/shift', [
            'worker_id' => 'test',
            'work_place_id' => 'test',
            'day' => '2020-06-21',
            'shift_start' => '09:00',
            'shift_end' => '21:00',
        ]);

        $response->assertSessionHasErrors('worker_id');
        $response->assertSessionHasErrors('work_place_id');
        $response->assertSessionDoesntHaveErrors('day');
        $response->assertSessionDoesntHaveErrors('shift_start');
        $response->assertSessionDoesntHaveErrors('shift_end');
    }

    /** @test */
    public function worker_id_and_work_place_id_must_be_unsigned_when_adding_and_updating()
    {
        $permission = setUpPermissionForUser();
        createShiftAndWorker($permission);

        signIn($permission['user']);

        $response = $this->post('api/shift', [
            'worker_id' => -1,
            'work_place_id' => -1,
            'day' => '2020-06-21',
            'shift_start' => '09:00',
            'shift_end' => '21:00',
        ]);

        $response->assertSessionHasErrors('worker_id');
        $response->assertSessionHasErrors('work_place_id');
        $response->assertSessionDoesntHaveErrors('day');
        $response->assertSessionDoesntHaveErrors('shift_start');
        $response->assertSessionDoesntHaveErrors('shift_end');
    }

    /** @test */
    public function day_must_be_date_with_format_Y_m_d_when_adding_and_updating()
    {
        $permission = setUpPermissionForUser();
        createShiftAndWorker($permission);

        signIn($permission['user']);

        $response = $this->post('api/shift', [
            'worker_id' => 1,
            'work_place_id' => 1,
            'day' => '2020-06-21 21:00:00',
            'shift_start' => '09:00',
            'shift_end' => '21:00',
        ]);

        $response->assertSessionDoesntHaveErrors('worker_id');
        $response->assertSessionDoesntHaveErrors('work_place_id');
        $response->assertSessionHasErrors('day');
        $response->assertSessionDoesntHaveErrors('shift_start');
        $response->assertSessionDoesntHaveErrors('shift_end');
    }

    /** @test */
    public function shift_start_and_shift_end_must_be_date_with_format_H_i_when_adding_and_updating()
    {
        $permission = setUpPermissionForUser();
        createShiftAndWorker($permission);

        signIn($permission['user']);

        $response = $this->post('api/shift', [
            'worker_id' => 1,
            'work_place_id' => 1,
            'day' => '2020-06-21',
            'shift_start' => '09:00:00',
            'shift_end' => '21:00:00',
        ]);

        $response->assertSessionDoesntHaveErrors('worker_id');
        $response->assertSessionDoesntHaveErrors('work_place_id');
        $response->assertSessionDoesntHaveErrors('day');
        $response->assertSessionHasErrors('shift_start');
        $response->assertSessionHasErrors('shift_end');
    }
}
