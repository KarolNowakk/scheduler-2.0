<?php

namespace Tests\Feature\Permissions;

use App\Indisposition;
use App\MonthlyRequirments;
use App\Services\Check;
use App\Services\WorkerShiftCreator;
use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Tests\Helpers\SetUper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class WorkerShiftCreatorTest extends TestCase
{
    use RefreshDatabase;

    protected $setUper;

    public function setUp() : void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->setUper = new SetUper();
        $this->setUper->setUp();
    }

    /** @test */
    public function create_should_create_shift()
    {
        $worker = $this->setUper->getWorker();
        $req = $this->setUper->getRequirements();
        $shift = json_decode($req->friday)[0];
        $creator = new WorkerShiftCreator($worker, '2020-12-11', $shift);
        $creator->create();

        $this->assertCount(1, Shift::all());
    }
}
