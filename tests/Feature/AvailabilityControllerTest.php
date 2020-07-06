<?php

namespace Tests\Feature;

use App\Availability;
use App\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class AvailabilityControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_avaiability_can_be_created()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);
        $worker = factory(Worker::class)->create(['work_place_id' => $permissions['workPlace']->id]);
        $availability = factory(Availability::class)->raw();
        
        $response = $this->json('post', 'api/availability/' . $worker->id, $availability);

        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(1, Availability::all());
    }

    /** @test */
    public function an_avaiability_can_be_updated()
    {
        $this->withoutExceptionHandling();
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);
        $worker = factory(Worker::class)->create(['work_place_id' => $permissions['workPlace']->id]);
        $availability = factory(Availability::class)->create();
        $availabilityToSend = factory(Availability::class)->raw();
        
        $response = $this->json('put', 'api/availability/' . $availability->id, $availabilityToSend);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertEquals($availabilityToSend['start'], $availability->start);
    }
}
