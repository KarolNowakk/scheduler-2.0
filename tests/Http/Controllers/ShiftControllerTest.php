<?php

namespace Tests\Http\Controllers;

use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ShiftControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp() : void
    {
        parent::setUp();
        factory(WorkPlace::class)->create();
        factory(Worker::class)->create();
    }

    /** @test */
    public function a_shift_can_be_added_to_schedule()
    {
        $this->withoutExceptionHandling();

        Passport::actingAs(factory(User::class)->create());

        $response = $this->post('api/shift', [
            'worker_id' => 1,
            'work_place_id' => 1,
            'day' => '2020-06-21',
            'shift_start' => '09:00',
            'shift_end' => '21:00',
        ]);

        $this->assertCount(1, Shift::all());
    }

    /** @test */
    public function a_shift_can_be_updated()
    {
        $this->withoutExceptionHandling();

        factory(Shift::class)->create();
        Passport::actingAs(factory(User::class)->create());

        $response = $this->json('put', 'api/shift/1', [
            'shift_start' => '12:00',
            'shift_end' => '22:00',
        ]);

        $this->assertEquals(Shift::first()->shift_end, '22:00');
        $this->assertEquals(Shift::first()->shift_start, '12:00');
    }

    /** @test */
    public function a_shift_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        factory(Shift::class)->create();
        Passport::actingAs(factory(User::class)->create());

        $this->delete('api/shift/1');

        $this->assertCount(0, Shift::all());
    }

    /** @test */
    public function a_shift_can_not_be_added_to_schedule_by_not_logged_user()
    {
        $response = $this->post('api/shift', [
            'worker_id' => 1,
            'work_place_id' => 1,
            'day' => '2020-06-21',
            'shift_start' => '09:00',
            'shift_end' => '21:00',
        ]);

        $this->assertCount(0, Shift::all());
    }

    /** @test */
    public function a_shift_can_not_be_updated_by_not_logged_user()
    {
        factory(Shift::class)->create();

        $response = $this->json('put', 'api/shift/1', [
            'shift_start' => '12:00',
            'shift_end' => '22:00',
        ]);

        $this->assertNotEquals(Shift::first()->shift_end, '22:00');
        $this->assertNotEquals(Shift::first()->shift_start, '12:00');
    }

    /** @test */
    public function a_shift_can_not_be_deleted_by_not_logged_user()
    {
        factory(Shift::class)->create();

        $this->delete('api/shift/1');

        $this->assertCount(1, Shift::all());
    }

    /** @test */
    public function all_fields_are_required_when_adding_shift()
    {
        Passport::actingAs(factory(User::class)->create());

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
        Passport::actingAs(factory(User::class)->create());

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
        Passport::actingAs(factory(User::class)->create());

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
        Passport::actingAs(factory(User::class)->create());

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
        Passport::actingAs(factory(User::class)->create());

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
