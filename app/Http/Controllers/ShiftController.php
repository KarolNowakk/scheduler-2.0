<?php

namespace App\Http\Controllers;

use App\Shift;
use App\WorkPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ShiftController extends Controller
{
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

        $created = Shift::create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Shift has been added to schedule'], ResponseStatus::HTTP_OK);
    }

    /**
     * Update existing data
     *
     * @param Response $response
     * @param Shift $shift
     * @return json
     */
    public function update(Request $request, Shift $shift)
    {
        $data = $this->updateValidator($request->all())->validate();
        $this->checkPermissions($request);
        $data['updated_by'] = Auth::id();

        if (!$shift->update($data)) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Shift has been updated'], ResponseStatus::HTTP_OK);
    }

    /**
     * Delete Worker
     *
     * @param Shift $shift
     * @return json
     */
    public function destroy(Shift $shift)
    {
        $this->checkPermissions($shift);
        if (!$shift->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Shift has been deleted'], ResponseStatus::HTTP_OK);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @param string $reqestType
     * @return Validator
     */
    protected function storeValidator(array $data)
    {
        return Validator::make($data, [
            'worker_id' => 'numeric|min:0|required',
            'work_place_id' => 'numeric|min:0|required',
            'day' => 'date_format:Y-m-d|required',
            'shift_start' => 'date_format:H:i|required',
            'shift_end' => 'date_format:H:i|required',
        ]);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @param string $reqestType
     * @return Validator
     */
    protected function updateValidator(array $data)
    {
        return Validator::make($data, [
            'worker_id' => 'numeric|min:0',
            'work_place_id' => 'required|numeric|min:0',
            'day' => 'date_format:Y-m-d',
            'shift_start' => 'date_format:H:i',
            'shift_end' => 'date_format:H:i',
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
        } elseif ($item instanceof Shift) {
            $workPlace = $item->workPlace;
        }

        if (Gate::denies('edit', $workPlace)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }
    }
}
