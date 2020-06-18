<?php

namespace Tests\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\WorkPlace;
use Illuminate\Support\Facades\Log;

/**
 * @internal
 * @coversNothing
 */
class WorkPlaceControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function a_workf_place_can_be_added()
    {
        $this->withoutExceptionHandling();
        $this->addWorkPlace();

        $this->assertCount(1, WorkPlace::all());
    }

    /**
     * Add Valid data except told otherwise
     */
    protected function addWorkPlace(
        $name = 'TestWorkPlace',
        $logo = 'public/photos/logos/testlogo.jpg',
        $adress = 'Test Street 1, Test City'
    ) {
        Log::info($name);
        return $this->post('/api/work_place', [
            'name' => $name,
            'logo_path' => $logo,
            'adress' => $adress,
        ]);
    }
}
