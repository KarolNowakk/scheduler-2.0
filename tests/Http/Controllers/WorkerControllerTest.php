<?php

namespace Tests\Http\Controllers;

use App\User;
use App\Worker;
use App\WorkPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class WorkerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_single_worker_can_be_shown()
    {
        $user = signIn();

        $workPlace = factory(WorkPlace::class)->create();
        $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id, 'user_id' => $user->id]);

        $response = $this->get('api/worker/' . $worker->id);

        $response->assertStatus(ResponseStatus::HTTP_OK)
            ->assertJsonStructure([
                'name',
                'short_name',
                'job_title',
                'work_place',
                'salary',
                'created_by',
                'belongs_to',
            ]);
    }

    /** @test */
    public function a_worker_can_be_created()
    {
        $this->withoutExceptionHandling();
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $worker = factory(Worker::class)->raw([
            'work_place_id' => $permissions['workPlace'],
        ]);
        $response = $this->json('post', 'api/worker', $worker);

        $this->assertCount(1, Worker::all());
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    /** @test */
    public function a_worker_can_be_updated()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $existingWorker = factory(Worker::class)->create([
            'work_place_id' => $permissions['workPlace'],
        ]);

        $dataForWorkerUpdate = factory(Worker::class)->raw();
        $response = $this->json('put', 'api/worker/' . $existingWorker->id, $dataForWorkerUpdate);

        $this->assertEquals($dataForWorkerUpdate['name'], Worker::first()->name);
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    /** @test */
    public function a_worker_can_be_deleted()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $worker = factory(Worker::class)->create([
            'work_place_id' => $permissions['workPlace']->id,
        ]);

        $response = $this->delete('api/worker/' . $worker->id);
        
        $this->assertCount(0, Worker::all());
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    /** @test */
    public function a_name_is_required_when_adding_or_updating()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $response = $this->addWorker($name = '');
        
        $response->assertSessionHasErrors('name');
        $response->assertSessionDoesntHaveErrors('short_name');
        $response->assertSessionDoesntHaveErrors('job_title');
        $response->assertSessionDoesntHaveErrors('salary');
        $response->assertSessionDoesntHaveErrors('work_place_id');
    }

    /** @test */
    public function a_name_short_name_and_job_title_must_be_strings_when_adding_or_updating()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $response = $this->addWorker($name = 1, $shortName = 1, $jobTitle = 1);
        
        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('short_name');
        $response->assertSessionHasErrors('job_title');
        $response->assertSessionDoesntHaveErrors('salary');
        $response->assertSessionDoesntHaveErrors('work_place_id');
    }

    /** @test */
    public function a_name_short_name_and_job_title_must_have_maximum_length_50_characters_when_adding_or_updating()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $response = $this->addWorker(
            $name = str_repeat('t', 51),
            $shortName = str_repeat('t', 51),
            $jobTitle = str_repeat('t', 51)
        );
        
        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('short_name');
        $response->assertSessionHasErrors('job_title');
        $response->assertSessionDoesntHaveErrors('salary');
        $response->assertSessionDoesntHaveErrors('work_place_id');
    }

    /** @test */
    public function salary_must_be_unsigned_when_adding_or_updating()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $response = $this->addWorker(
            $name = 'Test Worker',
            $shortName = 'Test',
            $jobTitle = 'Tester',
            $salary = -1,
            $workPlaceId = -1
        );
        $response->assertSessionDoesntHaveErrors('name');
        $response->assertSessionDoesntHaveErrors('short_name');
        $response->assertSessionDoesntHaveErrors('job_title');
        $response->assertSessionHasErrors('salary');
    }

    /** @test */
    public function a_worker_can_not_be_added_by_not_logged_in_user()
    {
        $this->post('/api/worker', [
            'name' => 'Test Worker',
            'short_name' => 'Test',
            'job_title' => 'Tester',
            'work_place_id' => 1,
            'salary' => 20,
        ]);

        $this->assertCount(0, Worker::all());
    }

    /** @test */
    public function a_worker_can_not_be_updated_by_not_logged_in_user()
    {
        $workPlace = factory(WorkPlace::class)->create();
        $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id]);
        $workerDataForUpdate = factory(Worker::class)->raw();

        $response = $this->json('put', '/api/worker/' . $worker->id, $workerDataForUpdate);
        
        $this->assertNotEquals($workerDataForUpdate['name'], Worker::first()->name);
    }

    /**
     * Add Valid data except told otherwise
     */
    protected function addWorker(
        $name = 'Test Worker',
        $shortName = 'Test',
        $jobTitle = 'Tester',
        $salary = 18,
        $workPlaceId = 1
    ) {
        return $this->post('/api/worker', [
            'name' => $name,
            'short_name' => $shortName,
            'job_title' => $jobTitle,
            'work_place_id' => $workPlaceId,
            'salary' => $salary,
        ]);
    }
}
