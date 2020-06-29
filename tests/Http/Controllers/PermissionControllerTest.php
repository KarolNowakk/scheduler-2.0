<?php

namespace Tests\Feature;

use App\User;
use App\WorkPlace;
use App\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_permission_can_be_granted()
    {
        $this->post('api/permission', [
            'user_id' => 1,
            'work_place_id' => 1,
            'type' => 'can_edit'
        ]);
    }
}
