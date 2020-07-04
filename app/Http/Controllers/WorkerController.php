<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkerResource;
use App\Worker;
use App\WorkPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use Illuminate\Support\Facades\Gate;

class WorkerController extends Controller
{
    /**
     * Show single worker
     *
     * @param Worker
     * @return WorkerResource
     */
    public function show(Worker $worker)
    {
        if (Gate::denies('accessToView', $worker->workPlace)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }
        return new WorkerResource($worker);
    }

    /**
     * Show all workers
     *
     * @param Worker
     * @return WorkerResource
     */
    public function index(WorkPlace $workPlace)
    {
        if ($workPlace == null) {
            return WorkerResource::collection(Worker::all());
        }

        return WorkerResource::collection($workPlace->workers);
    }

    /**
     * Store newly created data
     *
     * @param Response $response
     * @return json
     */
    public function store(Request $request)
    {
        $data = $this->storeValidator($request->all())->validate();
        $this->checkPermissions($request);
        
        $data['created_by'] = Auth::id();

        $created = Worker::create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Work place has been created'], ResponseStatus::HTTP_OK);
    }

    /**
    * Update existing data
    *
    * @param Response $response
    * @param Worker $worker
    * @return json
    */
    public function update(Request $request, Worker $worker)
    {
        $data = $this->updateValidator($request->all())->validate();
        $this->checkPermissions($worker);
        $data['updated_by'] = Auth::id();

        if (!$worker->update($data)) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Worker has been updated'], ResponseStatus::HTTP_OK);
    }

    /**
     * Delete Worker
     *
     * @param Worker $workPlace
     * @return json
     */
    public function destroy(Worker $worker)
    {
        $this->checkPermissions($worker);
        if (!$worker->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Work place has been deleted'], ResponseStatus::HTTP_OK);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @return Validator
     */
    public function storeValidator($data)
    {
        return Validator::make($data, [
            'name' => 'string|max:50|required',
            'short_name' => 'string|max:50',
            'job_title' => 'string|max:50',
            'salary' => 'numeric|min:0',
            'work_place_id' => 'numeric|min:0',
        ]);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @return Validator
     */
    public function updateValidator($data)
    {
        return Validator::make($data, [
            'name' => 'string|max:50|required',
            'short_name' => 'string|max:50',
            'job_title' => 'string|max:50',
            'salary' => 'numeric|min:0',
        ]);
    }

    /**
     * Check users permissions
     *
     * @param Request $request
     * @return json : void
     */
    protected function checkPermissions($item)
    {
        if ($item instanceof Request) {
            $workPlace = WorkPlace::findOrFail($item->get('work_place_id'));
        } elseif ($item instanceof Worker) {
            $workPlace = $item->workPlace;
        }

        if (Gate::denies('edit', $workPlace)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }
    }
}
