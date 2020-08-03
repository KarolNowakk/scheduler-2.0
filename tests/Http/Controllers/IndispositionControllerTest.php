<?php

namespace Tests\Http\Controllers;

use App\Indisposition;
use App\Worker;
use App\WorkPlace;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class IndispositionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $workPlace;
    protected $worker;
    protected $user;

    protected function setUp() : void
    {
        parent::setUp();

        $this->workPlace = factory(WorkPlace::class)->create();
        factory(User::class)->create();
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
    public function an_indisposition_can_be_created()
    {
        $this->withoutExceptionHandling();

        $Indisposition = factory(Indisposition::class)->raw();
        
        $response = $this->json('post', 'api/indisposition/' . $this->worker->id, $Indisposition);
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(1, Indisposition::all());
    }

    /** @test */
    public function an_indisposition_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $Indisposition = factory(Indisposition::class)->create(['worker_id' => $this->worker->id]);
        $IndispositionToSend = factory(Indisposition::class)->raw();
        
        $response = $this->json('put', 'api/indisposition/' . $Indisposition->id, $IndispositionToSend);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertEquals($IndispositionToSend['start'], Indisposition::first()->start);
    }

    /** @test */
    public function an_indisposition_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $Indisposition = factory(Indisposition::class)->create(['worker_id' => $this->worker->id]);
        
        $response = $this->json('delete', 'api/indisposition/' . $Indisposition->id);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(0, Indisposition::all());
    }

    // ---------------------------------------------------------

    /** @test */
    public function an_indisposition_can_not_be_created_by_user_without_permissions()
    {
        $userWithoutPermissions = factory(User::class)->create();
        Passport::actingAs($userWithoutPermissions);

        $Indisposition = factory(Indisposition::class)->raw();
        
        $response = $this->json('post', 'api/indisposition/' . $this->worker->id, $Indisposition);
        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertCount(0, Indisposition::all());
    }

    /** @test */
    public function an_indisposition_can_not_be_updated_by_user_without_permissions()
    {
        $this->withoutExceptionHandling();

        $Indisposition = factory(Indisposition::class)->create(['worker_id' => $this->worker->id]);
        
        $userWithoutPermissions = factory(User::class)->create();
        Passport::actingAs($userWithoutPermissions);

        $response = $this->json('delete', 'api/indisposition/' . $Indisposition->id);
        
        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertCount(1, Indisposition::all());
    }

    /** @test */
    public function an_indisposition_can_not_be_deleted_by_user_without_permissions()
    {
        $userWithoutPermissions = factory(User::class)->create();
        $Indisposition = factory(Indisposition::class)->create(['worker_id' => $this->worker->id]);

        Passport::actingAs($userWithoutPermissions);

        $IndispositionDataToSend = factory(Indisposition::class)->raw();
        
        $response = $this->json('put', 'api/indisposition/' . $Indisposition->id, $IndispositionDataToSend);
        $response->assertStatus(ResponseStatus::HTTP_FORBIDDEN);
        $this->assertNotEquals($IndispositionDataToSend['start'], Indisposition::first()->start);
    }

    // ---------------------------------------------------------

    /** @test */
    public function day_start_and_end_are_required_when_creating()
    {
        // $this->withoutExceptionHandling();
        $response = $this->post('api/indisposition/' . $this->worker->id, [
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
        $response = $this->post('api/indisposition/' . $this->worker->id, [
            'day' => 'fasd',
            'start' => 'sdaf',
            'end' => 'sdaf',
        ]);
        
        $response->assertSessionHasErrors('day');
        $response->assertSessionHasErrors('start');
        $response->assertSessionHasErrors('end');
    }
}
