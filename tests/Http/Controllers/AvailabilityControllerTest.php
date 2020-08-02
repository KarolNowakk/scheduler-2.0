<?php

namespace Tests\Http\Controllers;

use App\Availability;
use App\Worker;
use App\WorkPlace;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class AvailabilityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $workPlace;
    protected $worker;
    protected $user;

    protected function setUp() : void
    {
        parent::setUp();

        $this->workPlace = factory(WorkPlace::class)->create();
        $this->user = signIn();
        $this->worker = factory(Worker::class)->create([
            'user_id' => $this->user->id,
            'work_place_id' => $this->workPlace->id
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->setClassVariablesToNull();
    }

    protected function setClassVariablesToNull()
    {
        $this->workPlace = null;
        $this->worker = null;
        $this->user = null;
    }

    /** @test */
    public function an_avaiability_can_be_created()
    {
        $this->withoutExceptionHandling();

        $availability = factory(Availability::class)->raw();
        
        $response = $this->json('post', 'api/availability/' . $this->worker->id, $availability);
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(1, Availability::all());
    }

    /** @test */
    public function an_avaiability_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $availability = factory(Availability::class)->create(['worker_id' => $this->worker->id]);
        $availabilityToSend = factory(Availability::class)->raw();
        
        $response = $this->json('put', 'api/availability/' . $availability->id, $availabilityToSend);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertEquals($availabilityToSend['start'], Availability::first()->start);
    }

    /** @test */
    public function an_avaiability_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $availability = factory(Availability::class)->create(['worker_id' => $this->worker->id]);
        
        $response = $this->json('delete', 'api/availability/' . $availability->id);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(0, Availability::all());
    }

    // ---------------------------------------------------------

    /** @test */
    public function an_avaiability_can_not_be_created_by_user_without_permissions()
    {
        $userWithoutPermissions = factory(User::class)->create();
        Passport::actingAs($userWithoutPermissions);

        $availability = factory(Availability::class)->raw();
        
        $response = $this->json('post', 'api/availability/' . $this->worker->id, $availability);
        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertCount(0, Availability::all());
    }

    /** @test */
    public function an_avaiability_can_not_be_updated_by_user_without_permissions()
    {
        $this->withoutExceptionHandling();

        $availability = factory(Availability::class)->create(['worker_id' => $this->worker->id]);
        
        $userWithoutPermissions = factory(User::class)->create();
        Passport::actingAs($userWithoutPermissions);

        $response = $this->json('delete', 'api/availability/' . $availability->id);
        
        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertCount(1, Availability::all());
    }

    /** @test */
    public function an_avaiability_can_not_be_deleted_by_user_without_permissions()
    {
        $userWithoutPermissions = factory(User::class)->create();
        $availability = factory(Availability::class)->create(['worker_id' => $this->worker->id]);

        Passport::actingAs($userWithoutPermissions);

        $availabilityDataToSend = factory(Availability::class)->raw();
        
        $response = $this->json('put', 'api/availability/' . $availability->id, $availabilityDataToSend);
        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertNotEquals($availabilityDataToSend['start'], Availability::first()->start);
    }

    // ---------------------------------------------------------

    /** @test */
    public function day_start_and_end_are_required_when_creating()
    {
        // $this->withoutExceptionHandling();
        $response = $this->post('api/availability/' . $this->worker->id, [
            'day' => '',
            'start' => '',
            'end' => '',
        ]);
        
        $response->assertSessionHasErrors('day');
        $response->assertSessionHasErrors('start');
        $response->assertSessionHasErrors('end');
    }

    /** @test */
    public function day_start_and_end_has_to_be_date_when_creating()
    {
        // $this->withoutExceptionHandling();
        $response = $this->post('api/availability/' . $this->worker->id, [
            'day' => 'fasd',
            'start' => 'sdaf',
            'end' => 'sdaf',
        ]);
        
        $response->assertSessionHasErrors('day');
        $response->assertSessionHasErrors('start');
        $response->assertSessionHasErrors('end');
    }
}
