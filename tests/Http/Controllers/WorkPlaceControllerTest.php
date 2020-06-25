<?php

namespace Tests\Http\Controllers;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\WorkPlace;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use Laravel\Passport\Passport;

/**
 * @internal
 * @coversNothing
 */
class WorkPlaceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_single_work_place_can_be_shown()
    {
        $this->withoutExceptionHandling();

        factory(WorkPlace::class)->create();

        Passport::actingAs(factory(User::class)->create());

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
        $this->withoutExceptionHandling();

        factory(WorkPlace::class, 3)->create();
        Passport::actingAs(factory(User::class)->create());

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
        $this->withoutExceptionHandling();

        $response = $this->addWorkPlace();

        $this->assertCount(1, WorkPlace::all());
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    /** @test */
    public function a_work_place_can_be_updated()
    {
        $this->withoutExceptionHandling();

        factory(WorkPlace::class)->create();

        $response = $this->updateWorkPlace($name = 'TestUpdate');
        
        $this->assertEquals('TestUpdate', WorkPlace::first()->name);
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

    /** @test */
    public function a_work_place_can_be_deleted()
    {
        $this->withoutExceptionHandling();
                
        factory(WorkPlace::class)->create();
        Passport::actingAs(factory(User::class)->create());

        $response = $this->delete('api/work_place/1');
        
        $this->assertCount(0, WorkPlace::all());
        $response->assertStatus(ResponseStatus::HTTP_OK);
    }

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

    /**
     * Add Valid data except told otherwise
     */
    protected function addWorkPlace(
        $name = 'TestWorkPlace',
        $logo = 'public/photos/logos/testlogo.jpg',
        $address = 'Test Street 1, Test City'
    ) {
        Passport::actingAs(
            factory(User::class)->create(),
            ['create-servers']
        );
        return $this->post('/api/work_place', [
            'name' => $name,
            'logo_path' => $logo,
            'address' => $address,
        ]);
    }

    /**
     * Update
     */
    protected function updateWorkPlace(
        $name = 'TestWorkPlace',
        $logo = 'public/photos/logos/testlogo.jpg',
        $address = 'Test Street 1, Test City'
    ) {
        Passport::actingAs(factory(User::class)->create());

        return $this->json('put', '/api/work_place/1', [
            'name' => $name,
            'logo_path' => $logo,
            'address' => $address,
        ]);
    }
}
