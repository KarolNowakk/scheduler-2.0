<?php

namespace Tests\Http\Controllers;

use App\User;
use App\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\WorkPlace;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use Laravel\Passport\Passport;

/**
 * @internal
 *
 */
class WorkPlaceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    //-------------------------------------------- Controllers Methods Testing --------------------------------------------
    /** @test */
    public function a_single_work_place_can_be_shown()
    {
        factory(WorkPlace::class)->create();
        signIn();

        $response = $this->get('api/work_place/1');

        $response->assertStatus(ResponseStatus::HTTP_OK)
            ->assertJsonStructure([
                'name',
                'logo_path',
                'address'
            ]);
    }

    /** @test */
    public function a_multiple_work_places_can_be_showed()
    {
        factory(WorkPlace::class, 3)->create();
        signIn();

        $response = $this->get('api/work_place');

        $response->assertStatus(ResponseStatus::HTTP_OK)
            ->assertJsonStructure([
            [
                'name',
                'logo_path',
                'address',
            ]
        ]);
    }

    /** @test */
    public function a_work_place_can_be_created()
    {
        $response = $this->addWorkPlace();

        $this->assertCount(1, WorkPlace::all());
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    /** @test */
    public function a_work_place_can_be_updated_by_user_with_permissions()
    {
        $permissions = setUpPermissionForUser();

        signIn($permissions['user']);
        
        $response = $this->json('put', '/api/work_place/' . $permissions['workPlace']->id, [
            'name' => 'TestUpdate',
            'logo_path' => 'public/photos/logos/testlogo.jpg',
            'address' => 'Test Street 1, Test City',
        ]);

        $this->assertEquals('TestUpdate', WorkPlace::first()->name);
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }


    /** @test */
    public function a_work_place_can_be_deleted_by_its_creator()
    {
        $creator_of_work_place = factory(User::class)->create();
        $workPlace = factory(WorkPlace::class)->create(['created_by' => $creator_of_work_place->id]);
        
        signIn($creator_of_work_place);

        $response = $this->delete('api/work_place/1');
        
        $this->assertCount(0, WorkPlace::all());
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    //-------------------------------------------- Testing Controllers Permissions --------------------------------------------

    /** @test */
    public function a_work_place_cannot_be_added_by_not_logged_user()
    {
        $this->json('post', '/api/work_place', [
            'name' => 'TestName',
            'logo_path' => 'public/photos/logos/testlogo.jpg',
            'address' => 'Test Street 1, Test City',
        ]);
        $this->assertCount(0, WorkPlace::all());
    }

    /** @test */
    public function a_work_place_cannot_be_updated_by_not_logged_user()
    {
        factory(WorkPlace::class)->create();

        $this->json('put', '/api/work_place/1', [
            'name' => 'TestUpdate',
            'logo_path' => 'public/photos/logos/testlogo.jpg',
            'address' => 'Test Street 1, Test City',
        ]);

        $this->assertNotEquals('Test', WorkPlace::first()->name);
    }

    /** @test */
    public function a_work_place_cannot_be_deleted_by_not_logged_user()
    {
        factory(WorkPlace::class)->create();

        $this->json('delete', '/api/work_place/1', [
            'name' => 'TestUpdate',
            'logo_path' => 'public/photos/logos/testlogo.jpg',
            'address' => 'Test Street 1, Test City',
        ]);

        $this->assertCount(1, WorkPlace::all());
    }

    /** @test */
    public function a_work_place_can_not_be_deleted_by_user_without_permissions()
    {
        $permissions = setUpPermissionForUser();
        signIn($permissions['user']);

        $response = $this->json('delete', '/api/work_place/' . $permissions['workPlace']->id);

        $this->assertCount(1, WorkPlace::all());
        $response->assertStatus(403);
    }

    /** @test */
    public function a_work_place_can_not_be_updated_by_user_without_permissions()
    {
        $permissions = setUpPermissionForUser();
        signIn();
        $response = $this->json('put', '/api/work_place/' . $permissions['workPlace']->id, factory(WorkPlace::class)->raw());

        $this->assertEquals($permissions['workPlace']->name, WorkPlace::first()->name);
        $response->assertStatus(403);
    }

    //-------------------------------------------- Testing Controllers Validation --------------------------------------------

    /** @test */
    public function a_name_is_required_for_creating_and_updating()
    {
        $response = $this->addWorkPlace($name = '');
                
        $response->assertSessionHasErrors('name');
        $response->assertSessionDoesntHaveErrors('logo_path');
        $response->assertSessionDoesntHaveErrors('address');
    }

    /** @test */
    public function name_logo_path_address_must_be_strings_when_creating_and_updating()
    {
        $response = $this->addWorkPlace($name = 1, $logo = 1, $address = 1);

        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('logo_path');
        $response->assertSessionHasErrors('address');
    }

    /** @test */
    public function name_logo_path_address_have_limited_length_for_creating_and_updating()
    {
        $response = $this->addWorkPlace(
            $name = str_repeat('t', 51),
            $logo = str_repeat('t', 1001),
            $address = str_repeat('t', 256),
        );

        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('address');
        $response->assertSessionHasErrors('logo_path');
    }

    //-------------------------------------------- Allegedly useful methods, but it shall be deleted --------------------------------------------
    /**
     * Add Valid data except told otherwise
     */
    protected function addWorkPlace(
        $name = 'TestWorkPlace',
        $logo = 'public/photos/logos/testlogo.jpg',
        $address = 'Test Street 1, Test City'
    ) {
        signIn();
        return $this->post('/api/work_place', [
            'name' => $name,
            'logo_path' => $logo,
            'address' => $address,
        ]);
    }
}
