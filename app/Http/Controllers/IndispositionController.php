<?php

namespace App\Http\Controllers;

use App\Indisposition;
use App\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class IndispositionController extends Controller
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

        $created = $worker->Indisposition()->create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Indisposition has been created'], ResponseStatus::HTTP_OK);
    }

    /**
     * Upadate data
     *
     * @param Request $request
     * @param Indisposition $Indisposition
     * @return json
     */
    public function update(Request $request, Indisposition $Indisposition)
    {
        $data = $this->updatingValidator($request->all())->validate();

        if ($this->userHasNoPermissions($Indisposition)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }

        $updated = $Indisposition->update($data);

        if (!$updated) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Indisposition has been updated'], ResponseStatus::HTTP_OK);
    }

    /**
     * Delete Indisposition
     *
     * @param Shift $shift
     * @return json
     */
    public function destroy(Indisposition $Indisposition)
    {
        if ($this->userHasNoPermissions($Indisposition)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }

        if (!$Indisposition->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Indisposition has been deleted'], ResponseStatus::HTTP_OK);
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
            'month' => 'date_format:Y-m|required',
            'day' => 'date_format:d|required',
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
            'month' => 'date_format:Y-m',
            'day' => 'date_format:d',
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
        if ($item instanceof Indisposition) {
            $item = $item->worker;
        }
        return (Gate::denies('editIndisposition', $item));
    }
}
