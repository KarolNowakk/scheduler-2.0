<?php

namespace App\Http\Controllers;

use App\Availability;
use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class AvailabilityController extends Controller
{
    /**
     * Store newly created data
     *
     * @param Request $request
     * @param Worker $worker
     * @return json
     */
    public function store(Request $request, Worker $worker)
    {
        $data = $this->creatingValidator($request->all())->validate();
        
        if ($this->userHasNoPermissions($worker)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }

        $created = $worker->availability()->create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Availability has been created'], ResponseStatus::HTTP_OK);
    }

    /**
     * Upadate data
     *
     * @param Request $request
     * @param Availability $availability
     * @return json
     */
    public function update(Request $request, Availability $availability)
    {
        $data = $this->updatingValidator($request->all())->validate();

        if ($this->userHasNoPermissions($availability)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }

        $updated = $availability->update($data);

        if (!$updated) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Availability has been updated'], ResponseStatus::HTTP_OK);
    }

    /**
     * Delete Availability
     *
     * @param Shift $shift
     * @return json
     */
    public function destroy(Availability $availability)
    {
        if ($this->userHasNoPermissions($availability)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }

        if (!$availability->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Availability has been deleted'], ResponseStatus::HTTP_OK);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @param string $reqestType
     * @return Validator
     */
    protected function creatingValidator($data)
    {
        return Validator::make($data, [
            'day' => 'date_format:Y-m-d|required',
            'start' => 'date_format:H:i|required',
            'end' => 'date_format:H:i|required',
        ]);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @param string $reqestType
     * @return Validator
     */
    protected function updatingValidator($data)
    {
        return Validator::make($data, [
            'day' => 'date_format:Y-m-d',
            'start' => 'date_format:H:i',
            'end' => 'date_format:H:i',
        ]);
    }

    /**
     * Check users permissions
     *
     * @param Request $request
     * @return json : void
     */
    protected function userHasNoPermissions($item)
    {
        if ($item instanceof Availability) {
            $item = $item->worker;
        }
        return (Gate::denies('editAvailability', $item));
    }
}
