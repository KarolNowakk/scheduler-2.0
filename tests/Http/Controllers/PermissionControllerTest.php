<?php

namespace Tests\Feature;

use App\User;
use App\WorkPlace;
use App\Worker;
use App\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    public function a_permission_can_be_granted()
    {
        $userWithoutPermissions = factory(User::class)->create();

        $response = $this->json('post', 'api/permission', [
            'user_id' => $userWithoutPermissions->id,
            'work_place_id' => $this->workPlace->id,
            'type' => 'can_edit'
        ]);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(1, Permission::all());
    }

    /** @test */
    public function a_permission_can_be_deleted()
    {
        $userWithoutPermissions = factory(User::class)->create();

        $permission = factory(Permission::class)->create([
            'user_id' => $userWithoutPermissions->id,
            'work_place_id' => $this->workPlace->id
        ]);

        $response = $this->json('post', 'api/permission/'. $permission->id);
        
        $response->assertStatus(ResponseStatus::HTTP_OK);
        $this->assertCount(0, Permission::all());
    }
}
